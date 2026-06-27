from __future__ import annotations

import time
from typing import Optional

from bs4 import BeautifulSoup
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait

from scrapper import BogorTourismScraper, clean_tourism_dataframe


class BogorTourismSeleniumScraper(BogorTourismScraper):
    """Scraper yang mengambil HTML memakai Selenium WebDriver."""

    def __init__(self, delay: float = 1.0, scrape_details: bool = True, headless: bool = True):
        super().__init__(delay=delay, scrape_details=scrape_details)
        self.driver = self._create_driver(headless=headless)

    def _create_driver(self, headless: bool):
        options = Options()
        if headless:
            options.add_argument("--headless=new")
        options.add_argument("--disable-gpu")
        options.add_argument("--no-sandbox")
        options.add_argument("--disable-dev-shm-usage")
        options.add_argument("--window-size=1366,768")
        options.add_argument(
            "--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
            "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36"
        )
        return webdriver.Chrome(options=options)

    def _get_page(self, url: str) -> Optional[BeautifulSoup]:
        try:
            self.driver.get(url)
            WebDriverWait(self.driver, 30).until(
                lambda driver: driver.execute_script("return document.readyState") == "complete"
            )
            time.sleep(self.delay * 0.5)
            return BeautifulSoup(self.driver.page_source, "html.parser")
        except Exception as exc:
            print(f"❌ Selenium error: {exc}")
            return None

    def close(self):
        self.driver.quit()


def main():
    scraper = BogorTourismSeleniumScraper(delay=1.0, scrape_details=True, headless=True)
    try:
        all_items = scraper.scrape_all_categories()
        df_raw = scraper.to_dataframe(all_items)
        df_clean = clean_tourism_dataframe(df_raw)

        print("\n📊 KOLOM DATA:")
        print(df_clean.columns.tolist())

        print("\n📊 PREVIEW:")
        print(df_clean[["nama", "alamat", "harga_tiket", "jam_operasional"]].head(5))

        print(f"\nRaw: {len(df_raw)} destinasi")
        print(df_raw["kategori"].value_counts())

        print(f"\nClean: {len(df_clean)} destinasi")
        print(df_clean["kategori"].value_counts())

        df_clean.to_csv("bogor_tourism_data_clean_selenium.csv", index=False, encoding="utf-8-sig")
        df_clean.to_json(
            "bogor_tourism_data_clean_selenium.json",
            orient="records",
            force_ascii=False,
            indent=2,
        )
        print("💾 Saved: bogor_tourism_data_clean_selenium.csv")
        print("💾 Saved: bogor_tourism_data_clean_selenium.json")

        try:
            df_clean.to_excel("bogor_tourism_data_clean_selenium.xlsx", index=False, engine="openpyxl")
            print("💾 Saved: bogor_tourism_data_clean_selenium.xlsx")
        except Exception:
            print("⚠️ Install openpyxl for Excel export")

        return df_clean
    finally:
        scraper.close()


if __name__ == "__main__":
    df = main()
