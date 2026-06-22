# DU'A Fashion Frontend

  Website source files for [duafashion.store](https://duafashion.store).

  ## Structure

  ```
  public_html/
    index.php           ← Main storefront (PHP + HTML/CSS/JS)
    images/
      dua-logo-new.jpg  ← New DU'A Nigeria logo
  pos/application/views/
    sal-invoice-pos.php ← Thermal receipt (58mm) with Instagram QR
  ```

  ## Recent Fixes (June 2026)

  1. **Product images** — now show immediately on page load (removed lazy loading)
  2. **Receipt QR code** — scans to Instagram: [@dua.nig](https://www.instagram.com/dua.nig)
  3. **Receipt totals** — TOTAL / Cash Given / Change are plain text (no button borders)
  4. **Receipt centering** — receipt is centered in browser/PDF view
  5. **New logo** — DU'A Nigeria logo applied across site and receipt
  6. **Instagram link** — footer Instagram icon now links to [@dua.nig](https://www.instagram.com/dua.nig)
  