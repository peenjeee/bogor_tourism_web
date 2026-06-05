"""
Bogor Sport and Tourism Web Scraper - UPDATED
==============================================
Scraper dengan kolom terpisah untuk setiap informasi

Kolom yang diambil:
- nama, kategori, label, author, likes, url, url_gambar
- deskripsi (teks deskripsi saja)
- alamat
- fasilitas
- harga_tiket
- jam_operasional
- telepon
- sumber
- tags
"""

import requests
from bs4 import BeautifulSoup
import pandas as pd
import time
import re
from typing import Dict, List, Optional
from dataclasses import dataclass, asdict, field
import json

CATEGORIES = [
    "arena", "olahraga", "alam", "seni-budaya", 
    "belanja", "kuliner", "rekreasi",
]

BASE_URL = "https://sportandtourism.bogorkab.go.id"


def normalize_place_name(value: str) -> str:
    """Normalize destination names so duplicate titles can be removed safely."""
    return re.sub(r"[^a-z0-9]+", " ", str(value).lower()).strip()


def strip_multiline_trailing_spaces(value):
    if isinstance(value, str):
        return "\n".join(line.rstrip() for line in value.splitlines())
    return value


def clean_tourism_dataframe(df: pd.DataFrame) -> pd.DataFrame:
    """Return the canonical clean dataset.

    Canonical data keeps one row per URL first, then one row per normalized
    destination name. If two titles match, keep the higher-liked row.
    """
    if df.empty:
        return df.copy()

    clean = df.copy()
    text_cols = clean.select_dtypes(include=["object"]).columns
    clean[text_cols] = clean[text_cols].apply(lambda col: col.map(strip_multiline_trailing_spaces))
    clean["__row_order"] = range(len(clean))

    if "url" in clean.columns:
        clean = clean.drop_duplicates(subset="url", keep="first")

    if "nama" in clean.columns:
        clean["__nama_norm"] = clean["nama"].map(normalize_place_name)
        clean["__likes_sort"] = pd.to_numeric(clean.get("likes", 0), errors="coerce").fillna(0)
        clean = (
            clean.sort_values(
                ["__nama_norm", "__likes_sort", "__row_order"],
                ascending=[True, False, True],
            )
            .drop_duplicates(subset="__nama_norm", keep="first")
            .sort_values("__row_order")
        )

    helper_cols = ["__row_order", "__nama_norm", "__likes_sort"]
    return clean.drop(columns=[col for col in helper_cols if col in clean.columns]).reset_index(drop=True)

@dataclass
class TourismItem:
    """Data struktur untuk item wisata dengan kolom terpisah"""
    nama: str
    kategori: str
    label: str
    author: str
    likes: int
    url: str
    url_gambar: str
    deskripsi: str = ""
    alamat: str = ""
    fasilitas: str = ""
    harga_tiket: str = ""
    jam_operasional: str = ""
    telepon: str = ""
    sumber: str = ""
    tags: str = ""


class BogorTourismScraper:
    """Scraper dengan ekstraksi data terstruktur"""
    
    def __init__(self, delay: float = 1.0, scrape_details: bool = True):
        self.delay = delay
        self.scrape_details = scrape_details
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept': 'text/html,application/xhtml+xml',
            'Accept-Language': 'id-ID,id;q=0.9',
        })
    
    def _get_page(self, url: str) -> Optional[BeautifulSoup]:
        try:
            response = self.session.get(url, timeout=30)
            response.raise_for_status()
            return BeautifulSoup(response.text, 'html.parser')
        except requests.RequestException as e:
            print(f"❌ Error: {e}")
            return None
    
    def _extract_section_content(self, soup: BeautifulSoup, section_names: List[str]) -> str:
        """Ekstrak konten dari section berdasarkan heading"""
        for name in section_names:
            # Cari heading yang mengandung nama section
            headings = soup.find_all(['h2', 'h3', 'h4', 'strong', 'b'], 
                                     string=re.compile(name, re.IGNORECASE))
            for heading in headings:
                content = []
                # Ambil konten setelah heading
                next_elem = heading.find_next_sibling()
                while next_elem:
                    # Stop jika ketemu heading baru
                    if next_elem.name in ['h2', 'h3', 'h4']:
                        break
                    text = next_elem.get_text(strip=True)
                    if text:
                        content.append(text)
                    next_elem = next_elem.find_next_sibling()
                
                if content:
                    return ' | '.join(content)
                
                # Jika heading dalam <p>, ambil text setelahnya
                parent = heading.find_parent('p')
                if parent:
                    next_p = parent.find_next_sibling('p')
                    if next_p:
                        return next_p.get_text(strip=True)
        return ""
    
    def _get_detail_data(self, url: str) -> Dict[str, str]:
        """Mengambil data detail dari halaman destinasi"""
        result = {
            'deskripsi': '',
            'alamat': '',
            'fasilitas': '',
            'harga_tiket': '',
            'jam_operasional': '',
            'telepon': '',
            'sumber': '',
            'tags': ''
        }
        
        if not url:
            return result
        
        try:
            soup = self._get_page(url)
            if not soup:
                return result
            
            # Cari container konten utama
            content = soup.find('div', class_='wrap-fullwidth')
            if not content:
                content = soup.find('div', class_='entry-content')
            if not content:
                content = soup.find('article')
            
            if content:
                # Hapus elemen yang tidak diinginkan (form, footer, related posts)
                for unwanted in content.find_all(['form', 'footer', 'nav']):
                    unwanted.decompose()
                for unwanted in content.find_all(class_=re.compile(r'comment|reply|related|share|social')):
                    unwanted.decompose()
                
                # Ekstrak deskripsi (paragraf pertama sebelum heading)
                paragraphs = []
                for p in content.find_all('p'):
                    text = p.get_text(strip=True)
                    # Stop jika ketemu section header
                    if any(keyword in text.lower() for keyword in ['alamat', 'fasilitas', 'harga', 'tiket', 'jam operasional', 'telepon', 'sumber']):
                        break
                    if text and len(text) > 20:
                        paragraphs.append(text)
                result['deskripsi'] = '\n\n'.join(paragraphs[:5])  # Max 5 paragraf
                
                # Ekstrak Alamat
                alamat_patterns = ['Alamat', 'Lokasi', 'Location', 'Address']
                result['alamat'] = self._extract_section_content(content, alamat_patterns)
                
                # Jika tidak ketemu, coba cari pola alamat
                if not result['alamat']:
                    for p in content.find_all('p'):
                        text = p.get_text(strip=True)
                        if any(kw in text.lower() for kw in ['jl.', 'jalan', 'kecamatan', 'kabupaten', 'desa', 'kp.']):
                            result['alamat'] = text
                            break
                
                # Ekstrak Fasilitas
                fasilitas_section = content.find(string=re.compile(r'Fasilitas', re.IGNORECASE))
                if fasilitas_section:
                    parent = fasilitas_section.find_parent()
                    if parent:
                        ul = parent.find_next('ul')
                        if ul:
                            items = [li.get_text(strip=True) for li in ul.find_all('li')]
                            result['fasilitas'] = ' | '.join(items)
                
                # Ekstrak Harga Tiket
                harga_patterns = ['Harga Tiket', 'Tiket Masuk', 'HTM', 'Harga']
                result['harga_tiket'] = self._extract_section_content(content, harga_patterns)
                
                if not result['harga_tiket']:
                    harga_section = content.find(string=re.compile(r'Harga|Tiket', re.IGNORECASE))
                    if harga_section:
                        parent = harga_section.find_parent()
                        if parent:
                            ul = parent.find_next('ul')
                            if ul:
                                items = [li.get_text(strip=True) for li in ul.find_all('li')]
                                result['harga_tiket'] = ' | '.join(items)
                
                # Ekstrak Jam Operasional
                jam_patterns = ['Jam Operasional', 'Jam Buka', 'Waktu Operasional', 'Jam Kerja']
                result['jam_operasional'] = self._extract_section_content(content, jam_patterns)
                
                # Ekstrak Telepon
                telepon_patterns = ['Telepon', 'Telp', 'Phone', 'Kontak', 'No. HP']
                result['telepon'] = self._extract_section_content(content, telepon_patterns)
                
                # Jika tidak ketemu, cari pola nomor telepon
                if not result['telepon']:
                    phone_match = re.search(r'(\+62|08|021)[\d\-\s]{8,15}', content.get_text())
                    if phone_match:
                        result['telepon'] = phone_match.group(0)
                
                # Ekstrak Sumber
                sumber = content.find('a', href=re.compile(r'travelspromo|google|maps'))
                if sumber:
                    result['sumber'] = sumber.get('href', '')
            
            # Ekstrak Tags
            tags_container = soup.find(class_=re.compile(r'tag|article-tag'))
            if tags_container:
                tag_links = tags_container.find_all('a')
                result['tags'] = ', '.join([a.get_text(strip=True) for a in tag_links])
            
            return result
            
        except Exception as e:
            print(f"⚠️ Error detail: {e}")
            return result
    
    def _extract_items_from_page(self, soup: BeautifulSoup, kategori: str) -> List[TourismItem]:
        items = []
        
        container = soup.find('ul', id='infinite-articles')
        if not container:
            container = soup.find('ul', class_='masonry_list')
        if not container:
            container = soup.find('div', class_='content-masonry')
        
        if not container:
            return []
        
        list_items = container.find_all('li', class_=lambda x: x and ('post' in x or 'ex34' in x))
        
        for item in list_items:
            try:
                nama = ""
                title_elem = item.find('h3', class_='index-title')
                if title_elem:
                    link = title_elem.find('a')
                    nama = link.get_text(strip=True) if link else title_elem.get_text(strip=True)
                
                url = ""
                link_elem = item.find('a', href=True)
                if link_elem:
                    url = link_elem.get('href', '')
                    if url and not url.startswith('http'):
                        url = BASE_URL + url
                
                label = ""
                label_elem = item.find('a', rel='tag')
                if not label_elem:
                    label_elem = item.find('a', href=re.compile(r'/category/'))
                if label_elem:
                    label = label_elem.get_text(strip=True)
                
                author = ""
                meta = item.find('ul', class_='meta-content')
                if meta:
                    author_elem = meta.find('a')
                    if author_elem:
                        author = author_elem.get_text(strip=True)
                
                likes = 0
                likes_elem = item.find('span', class_='thumbs-rating-up')
                if likes_elem:
                    numbers = re.findall(r'\d+', likes_elem.get_text(strip=True))
                    if numbers:
                        likes = int(numbers[0])
                
                url_gambar = ""
                img = item.find('img')
                if img:
                    url_gambar = img.get('src') or img.get('data-src') or ""
                
                if nama:
                    items.append(TourismItem(
                        nama=nama,
                        kategori=kategori.replace('-', ' ').title(),
                        label=label,
                        author=author,
                        likes=likes,
                        url=url,
                        url_gambar=url_gambar
                    ))
                    
            except Exception as e:
                continue
        
        return items
    
    def _get_max_pages(self, soup: BeautifulSoup) -> int:
        max_page = 1
        page_links = soup.find_all('a', href=re.compile(r'/page/\d+'))
        for link in page_links:
            match = re.search(r'/page/(\d+)', link.get('href', ''))
            if match:
                max_page = max(max_page, int(match.group(1)))
        return max_page
    
    def scrape_category(self, kategori: str, max_pages: int = None) -> List[TourismItem]:
        kategori_lower = kategori.lower().replace(' ', '-')
        if kategori_lower not in CATEGORIES:
            print(f"❌ Kategori '{kategori}' tidak ditemukan.")
            return []
        
        all_items = []
        print(f"\n📂 Scraping: {kategori}")
        
        first_url = f"{BASE_URL}/category/{kategori_lower}/"
        soup = self._get_page(first_url)
        if not soup:
            return []
        
        actual_max = self._get_max_pages(soup)
        if max_pages:
            actual_max = min(actual_max, max_pages)
        print(f"   📄 Halaman: {actual_max}")
        
        items = self._extract_items_from_page(soup, kategori)
        all_items.extend(items)
        print(f"   ✓ Page 1: {len(items)} items")
        
        for page in range(2, actual_max + 1):
            time.sleep(self.delay)
            url = f"{BASE_URL}/category/{kategori_lower}/page/{page}/"
            soup = self._get_page(url)
            if not soup:
                break
            items = self._extract_items_from_page(soup, kategori)
            if not items:
                break
            all_items.extend(items)
            print(f"   ✓ Page {page}: {len(items)} items")
        
        # Ambil detail dari setiap halaman
        if self.scrape_details and all_items:
            print(f"   📝 Mengambil detail...")
            for i, item in enumerate(all_items):
                time.sleep(self.delay * 0.5)
                detail = self._get_detail_data(item.url)
                item.deskripsi = detail['deskripsi']
                item.alamat = detail['alamat']
                item.fasilitas = detail['fasilitas']
                item.harga_tiket = detail['harga_tiket']
                item.jam_operasional = detail['jam_operasional']
                item.telepon = detail['telepon']
                item.sumber = detail['sumber']
                item.tags = detail['tags']
                
                if (i + 1) % 10 == 0:
                    print(f"      {i + 1}/{len(all_items)}")
        
        print(f"   ✅ Total: {len(all_items)}")
        return all_items
    
    def scrape_all_categories(self, max_pages_per_category: int = None) -> List[TourismItem]:
        all_items = []
        print("=" * 50)
        print("🌴 BOGOR TOURISM SCRAPER (UPDATED)")
        print("=" * 50)
        
        for kategori in CATEGORIES:
            items = self.scrape_category(kategori, max_pages_per_category)
            all_items.extend(items)
            time.sleep(self.delay)
        
        print("\n" + "=" * 50)
        print(f"🎉 TOTAL: {len(all_items)} items")
        print("=" * 50)
        return all_items
    
    def to_dataframe(self, items: List[TourismItem]) -> pd.DataFrame:
        return pd.DataFrame([asdict(item) for item in items])
    
    def save_to_csv(self, items: List[TourismItem], filename: str = "bogor_tourism_data_clean.csv"):
        df = self.to_dataframe(items)
        df.to_csv(filename, index=False, encoding='utf-8-sig')
        print(f"💾 Saved: {filename}")
    
    def save_to_json(self, items: List[TourismItem], filename: str = "bogor_tourism_data_clean.json"):
        data = [asdict(item) for item in items]
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        print(f"💾 Saved: {filename}")
    
    def save_to_excel(self, items: List[TourismItem], filename: str = "bogor_tourism_data_clean.xlsx"):
        df = self.to_dataframe(items)
        df.to_excel(filename, index=False, engine='openpyxl')
        print(f"💾 Saved: {filename}")


def main():
    scraper = BogorTourismScraper(delay=1.0, scrape_details=True)
    all_items = scraper.scrape_all_categories()
    
    df_raw = scraper.to_dataframe(all_items)
    df_clean = clean_tourism_dataframe(df_raw)
    
    print("\n📊 KOLOM DATA:")
    print(df_clean.columns.tolist())
    
    print("\n📊 PREVIEW:")
    print(df_clean[['nama', 'alamat', 'harga_tiket', 'jam_operasional']].head(5))

    print(f"\nRaw: {len(df_raw)} destinasi")
    print(df_raw["kategori"].value_counts())

    print(f"\nClean: {len(df_clean)} destinasi")
    print(df_clean["kategori"].value_counts())

    df_clean.to_csv("bogor_tourism_data_clean.csv", index=False, encoding="utf-8-sig")
    df_clean.to_json("bogor_tourism_data_clean.json", orient="records", force_ascii=False, indent=2)
    print("💾 Saved: bogor_tourism_data_clean.csv")
    print("💾 Saved: bogor_tourism_data_clean.json")
    
    try:
        df_clean.to_excel("bogor_tourism_data_clean.xlsx", index=False, engine="openpyxl")
        print("💾 Saved: bogor_tourism_data_clean.xlsx")
    except:
        print("⚠️ Install openpyxl for Excel export")
    
    return df_clean


if __name__ == "__main__":
    df = main()
