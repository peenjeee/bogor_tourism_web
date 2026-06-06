# Script Data Wisata Bogor

Folder ini berisi script scraping dan hasil data bersih yang dipakai oleh
BogorXplore.

## Isi Folder

| File | Fungsi |
| --- | --- |
| `scrapper.py` | Script Python untuk scraping kategori wisata dari `sportandtourism.bogorkab.go.id`, membersihkan duplikat, lalu export data. |
| `scrapper.ipynb` | Notebook versi eksplorasi/interaktif dari proses scraping. |
| `bogor_tourism_data_clean.csv` | Dataset bersih utama. File ini diprioritaskan oleh `PlaceSeeder`. |
| `bogor_tourism_data_clean.json` | Dataset bersih dalam format JSON. |
| `bogor_tourism_data_clean.xlsx` | Dataset bersih dalam format Excel untuk review manual. |

## Dataset Clean

File clean saat ini berisi 296 destinasi wisata.

Kolom utama:

```text
nama, kategori, url, url_gambar, likes, isi, deskripsi
```

`isi` menyimpan konten lengkap hasil scraping, sedangkan `deskripsi` dipakai
sebagai teks destinasi yang dapat diproses oleh web dan API rekomendasi.

## Menjalankan Scraper

Dari folder ini:

```powershell
pip install requests beautifulsoup4 pandas openpyxl
python scrapper.py
```

Output yang dibuat ulang:

```text
bogor_tourism_data_clean.csv
bogor_tourism_data_clean.json
bogor_tourism_data_clean.xlsx
```

Scraping mengambil data dari website eksternal, jadi hasil bisa berubah jika
struktur halaman sumber berubah.

## Dipakai Oleh Laravel

Seeder Laravel membaca dataset dengan prioritas berikut:

1. `script/bogor_tourism_data_clean.csv`
2. `flask_api/data/bogor_tourism_data.csv`
3. `web_recommendation/database/seeders/data/bogor_tourism_data.csv`
4. fallback JSON di folder seeder, Flask API, atau `script`

Jalankan import dari folder `web_recommendation`:

```powershell
php artisan db:seed --class=PlaceSeeder
```

## Catatan

- Jangan edit file output clean secara manual kecuali memang ingin mengoreksi
  dataset final.
- Jika output di-regenerate, cek jumlah baris dan spot-check beberapa destinasi
  sebelum menjalankan seeder.
