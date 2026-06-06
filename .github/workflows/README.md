# GitHub Actions Workflows

Folder ini berisi workflow otomatis untuk project BogorXplore.

## `sync-flask-api-to-hf.yml`

Workflow ini melakukan sinkronisasi folder `flask_api/` ke Hugging Face Space
`Peenjeee/bogor-xplore-api`.

## Trigger

Workflow berjalan saat:

- ada push ke branch `main` yang mengubah `flask_api/**`
- ada push ke branch `main` yang mengubah
  `.github/workflows/sync-flask-api-to-hf.yml`
- workflow dijalankan manual melalui `workflow_dispatch`

## Secret yang Dibutuhkan

Tambahkan secret berikut di GitHub repository:

```text
HF_TOKEN
```

Lokasi:

```text
Settings -> Secrets and variables -> Actions -> New repository secret
```

Token harus punya akses push ke Hugging Face Space target.

## Alur Workflow

1. Checkout source code dari GitHub.
2. Pull Git LFS asset untuk `flask_api/data/**`.
3. Clone Hugging Face Space target.
4. Sync isi folder `flask_api/` ke repo Space memakai `rsync --delete`.
5. Commit perubahan jika ada.
6. Push ke branch `main` Hugging Face Space.

## Catatan Penting

- `rsync --delete` membuat isi Space mengikuti folder `flask_api/`.
- File di Space yang tidak ada di `flask_api/` akan terhapus saat sync.
- Data besar di `flask_api/data/**` perlu tersimpan lewat Git LFS agar workflow
  bisa menarik asset lengkap sebelum deploy.
- Dockerfile di `flask_api/Dockerfile` menjalankan API di port `7860`, sesuai
  konfigurasi Hugging Face Space.

## Troubleshooting

| Masalah | Solusi |
| --- | --- |
| `Missing HF_TOKEN secret` | Tambahkan `HF_TOKEN` di repository secrets. |
| Git LFS asset tidak lengkap | Pastikan file besar di `flask_api/data/**` tracked oleh Git LFS. |
| Push ke Hugging Face gagal | Pastikan token punya akses write ke Space `Peenjeee/bogor-xplore-api`. |
| File Space hilang setelah sync | Pastikan file tersebut memang ada di folder `flask_api/`, karena sync memakai `--delete`. |
