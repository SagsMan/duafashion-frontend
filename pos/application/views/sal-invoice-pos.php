<!DOCTYPE html>
  <html>
  <head><meta charset="utf-8">
  <title><?= $page_title;?></title>
  <?php include"comman/code_css_form.php"; ?>
  <style type="text/css">
      * { margin: 0; padding: 0; box-sizing: border-box; }

      /* Tiled watermark overlay (OPay-style) */
      body::after {
          content: '';
          position: fixed;
          top: 0; left: 0;
          width: 100%; height: 100%;
          pointer-events: none;
          z-index: 9999;
          background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='70'%3E%3Ctext x='70' y='40' text-anchor='middle' font-family='Arial,sans-serif' font-size='10' font-weight='900' fill='rgba(0%2C0%2C0%2C0.07)' transform='rotate(-35%2C70%2C35)' letter-spacing='2'%3EDUA FASHION%3C/text%3E%3C/svg%3E");
          background-repeat: repeat;
      }

      html {
          background: #fff;
      }

      body {
          font-family: 'Arial', Helvetica, sans-serif;
          font-size: 10px;
          width: 58mm;
          max-width: 58mm;
          margin: 0 auto;
          padding: 4px 3px;
          background: #fff;
          color: #111;
          position: relative;
      }

      /* Center on screen view (browser/PDF viewer) */
      @media screen {
          html {
              background: #f0f0f0;
              display: flex;
              justify-content: center;
              padding: 20px;
              min-height: 100vh;
          }
          body {
              margin: 0 auto;
              box-shadow: 0 2px 12px rgba(0,0,0,0.15);
              padding: 8px 6px;
          }
      }

      .center { text-align: center; }
      .left   { text-align: left; }
      .right  { text-align: right; }
      .bold   { font-weight: bold; }

      .logo-wrap { text-align: center; padding: 4px 0 2px 0; }
      .logo-wrap img { max-width: 80px; max-height: 50px; }

      .store-name {
          font-size: 13px;
          font-weight: 900;
          text-align: center;
          letter-spacing: 2px;
          text-transform: uppercase;
          padding-bottom: 3px;
          margin-bottom: 2px;
      }
      .store-info { text-align: center; font-size: 9px; line-height: 1.5; color: #444; }

      .divider       { border-top: 1px dashed #888; margin: 3px 0; }
      .divider-solid { border-top: 1.5px solid #111; margin: 3px 0; }

      .receipt-title {
          text-align: center;
          font-size: 10.5px;
          font-weight: 900;
          letter-spacing: 3px;
          margin: 3px 0;
          padding: 3px 0;
          text-transform: uppercase;
          background: #111;
          color: #fff;
      }

      .meta-table { width: 100%; font-size: 9px; border: none; border-collapse: collapse; }
      .meta-table td { padding: 1.5px 0; vertical-align: top; border: none; }
      .meta-table td:first-child { color: #555; font-weight: 700; white-space: nowrap; }
      .meta-table td:last-child  { text-align: right; font-weight: 700; color: #111; word-break: break-all; }

      .items-table { width: 100%; border-collapse: collapse; font-size: 9px; margin: 3px 0; }
      .items-table thead tr th {
          text-align: left;
          padding: 2px 1px;
          font-size: 8.5px;
          font-weight: 800;
          text-transform: uppercase;
          border-top: 1px solid #111;
          border-bottom: 1px dashed #666;
          border-left: none;
          border-right: none;
          letter-spacing: 0.4px;
      }
      .items-table tbody tr td { padding: 1.5px 1px; vertical-align: top; border: none; }
      .items-table tbody tr:nth-child(even) td { background: rgba(0,0,0,0.025); }
      .items-table .qty-col   { text-align: center; width: 16px; }
      .items-table .price-col { text-align: right;  width: 38px; }
      .items-table .total-col { text-align: right;  width: 40px; font-weight: 700; }

      .totals-table { width: 100%; font-size: 9px; margin-top: 2px; border: none; border-collapse: collapse; }
      .totals-table td { padding: 1.5px 1px; border: none !important; background: transparent !important; }
      .totals-table .label  { text-align: right; padding-right: 4px; color: #444; font-weight: normal; border: none !important; }
      .totals-table .amount { text-align: right; font-weight: 700; width: 48px; border: none !important; }

      /* Grand total row — plain dark background, NO button look */
      .grand-total td {
          font-size: 11.5px;
          font-weight: 900;
          background: #111 !important;
          color: #fff !important;
          padding: 4px 3px;
          border: none !important;
      }
      .grand-total .label  { color: #fff !important; text-align: right; padding-right: 4px; }
      .grand-total .amount { color: #fff !important; text-align: right; }

      /* Remove any inherited button styling that might come from Bootstrap */
      .totals-table button,
      .totals-table .btn {
          border: none !important;
          background: transparent !important;
          padding: 0 !important;
          font-size: inherit !important;
          font-weight: inherit !important;
          color: inherit !important;
          display: inline !important;
          cursor: default !important;
      }

      .footer {
          text-align: center;
          font-size: 9px;
          margin-top: 4px;
          line-height: 1.7;
          border-top: 1.5px double #111;
          padding-top: 4px;
      }
      .footer .no-refund {
          font-weight: 800;
          font-size: 9.5px;
          text-transform: uppercase;
          letter-spacing: 0.4px;
          color: #c00;
      }
      .footer .thanks {
          font-style: italic;
          font-size: 9.5px;
          color: #333;
      }

      .qr-wrap { text-align: center; margin: 6px 0 3px 0; }
      .qr-wrap img { max-width: 100px; height: auto; display: inline-block; }
      .qr-label { text-align: center; font-size: 7.5px; color: #666; margin-top: 2px; }

      @media print {
          .no-print { display: none !important; }
          body { margin: 0; padding: 2px; width: 58mm; }
          @page { margin: 0; size: 58mm auto; }
          body::after { position: fixed; }
          html { display: block; background: #fff; padding: 0; }
      }
  </style>
  </head>
  <body>

  <?php
  $CI =& get_instance();
  $q1=$this->db->query("select * from db_company where id=1 and status=1");
  $res1=$q1->row();

  $company_name               = $res1->company_name;
  $company_mobile             = $res1->mobile;
  $company_phone              = $res1->phone;
  $company_address            = $res1->address;
  $company_city               = $res1->city;
  $company_logo               = $res1->company_logo;

  $q3=$this->db->query("SELECT a.sales_due,a.customer_name,a.mobile,a.phone,
                     a.opening_balance,
                     b.sales_date,b.created_time,b.reference_no,
                     b.sales_code,b.sales_note,b.tot_discount_to_all_amt,
                     coalesce(b.grand_total,0) as grand_total,
                     coalesce(b.subtotal,0) as subtotal,
                     coalesce(b.paid_amount,0) as paid_amount,
                     coalesce(b.other_charges_input,0) as other_charges_input,
                     coalesce(b.other_charges_amt,0) as other_charges_amt,
                     b.discount_to_all_input,
                     b.discount_to_all_type,
                     coalesce(b.tot_discount_to_all_amt,0) as tot_discount_to_all_amt,
                     coalesce(b.round_off,0) as round_off,
                     b.payment_status,
                     b.created_by
                     FROM db_customers a, db_sales b 
                     WHERE a.id=b.customer_id AND b.id='$sales_id'");

  $res3=$q3->row();
  $customer_name      = $res3->customer_name;
  $customer_mobile    = $res3->mobile;
  $sales_date         = show_date($res3->sales_date);
  $created_time       = show_time($res3->created_time);
  $sales_code         = $res3->sales_code;
  $seller_name        = $res3->created_by;
  $customer_due       = $res3->sales_due;
  $total_discount     = $res3->tot_discount_to_all_amt;
  $grand_total        = $res3->grand_total;
  $other_charges_amt  = $res3->other_charges_amt;
  $paid_amount        = $res3->paid_amount;
  $discount_to_all_input  = $res3->discount_to_all_input;
  $discount_to_all_type   = $res3->discount_to_all_type;
  $tot_discount_to_all_amt= $res3->tot_discount_to_all_amt;
  $round_off          = $res3->round_off;
  $payment_status     = $res3->payment_status;

  $previous_due = $res3->sales_due - ($res3->grand_total - $res3->paid_amount);
  $previous_due = ($previous_due > 0) ? $previous_due : 0;
  ?>

  <!-- LOGO -->
  <div class="logo-wrap">
    <?php if(!empty($company_logo)): ?>
      <img src="<?= base_url('uploads/company/'.$company_logo); ?>" alt="Logo">
    <?php else: ?>
      <img src="<?= base_url('uploads/dua-logo-new.jpg'); ?>" alt="DU'A Logo">
    <?php endif; ?>
  </div>

  <!-- STORE HEADER -->
  <div class="store-name"><?= strtoupper($company_name); ?></div>
  <div class="store-info">
      <?php if(!empty(trim($company_address))): ?>
      <?= $company_address; ?><?= !empty($company_city) ? ', '.$company_city : ''; ?><br>
      <?php endif; ?>
      <?php if(!empty(trim($company_mobile))): ?>
      Tel: <?= $company_mobile; ?><?= !empty($company_phone) ? ' / '.$company_phone : ''; ?>
      <?php endif; ?>
  </div>

  <div class="divider-solid"></div>
  <div class="receipt-title">&#9632; RECEIPT &#9632;</div>
  <div class="divider-solid"></div>

  <!-- META INFO -->
  <table class="meta-table">
      <tr>
          <td class="bold">Receipt No:</td>
          <td class="right"><?= $sales_code; ?></td>
      </tr>
      <tr>
          <td class="bold">Date:</td>
          <td class="right"><?= $sales_date; ?></td>
      </tr>
      <tr>
          <td class="bold">Time:</td>
          <td class="right"><?= $created_time; ?></td>
      </tr>
      <tr>
          <td class="bold">Sales Rep:</td>
          <td class="right"><?= ucfirst($seller_name); ?></td>
      </tr>
      <?php if(!empty(trim($customer_name)) && strtolower($customer_name) != 'walk-in customer'): ?>
      <tr>
          <td class="bold">Customer:</td>
          <td class="right"><?= $customer_name; ?></td>
      </tr>
      <?php endif; ?>
  </table>

  <div class="divider"></div>

  <!-- ITEMS TABLE -->
  <table class="items-table">
      <thead>
          <tr>
              <th style="width:40%">Item</th>
              <th class="qty-col">Qty</th>
              <th class="price-col">Price</th>
              <th class="total-col">Total</th>
          </tr>
      </thead>
      <tbody>
          <?php
          $i = 0;
          $subtotal = 0;
          $tax_amt = 0;
          $item_discount = 0;
          $q2=$this->db->query("select b.sales_price, a.discount_type, a.discount_input, a.discount_amt,
                                b.item_name, a.sales_qty, a.unit_total_cost, a.price_per_unit,
                                a.tax_amt, c.tax, a.total_cost
                                from db_salesitems a, db_items b, db_tax c
                                where c.id=a.tax_id and b.id=a.item_id and a.sales_id='$sales_id'");
          foreach ($q2->result() as $res2) {
              $i++;
              echo "<tr>";
              echo "<td>".$res2->item_name."</td>";
              echo "<td class='qty-col'>".$res2->sales_qty."</td>";
              echo "<td class='price-col'>".number_format($res2->price_per_unit, 2)."</td>";
              echo "<td class='total-col'>".number_format($res2->total_cost, 2)."</td>";
              echo "</tr>";
              if($res2->discount_amt > 0){
                  echo "<tr><td colspan='4' style='font-size:8px;padding-left:4px;color:#888;border:none;'>  Disc: -".number_format($res2->discount_amt,2)."</td></tr>";
              }
              $subtotal += $res2->total_cost;
              $tax_amt  += $res2->tax_amt;
              $item_discount += $res2->discount_amt;
          }
          $before_tax = $subtotal - $tax_amt;
          ?>
      </tbody>
  </table>

  <div class="divider"></div>

  <!-- TOTALS -->
  <table class="totals-table">
      <?php if($tax_amt > 0 && !is_tax_disabled()): ?>
      <tr>
          <td class="label">Subtotal:</td>
          <td class="amount"><?= number_format($before_tax, 2); ?></td>
      </tr>
      <tr>
          <td class="label">Tax:</td>
          <td class="amount"><?= number_format($tax_amt, 2); ?></td>
      </tr>
      <?php endif; ?>
      <?php if($other_charges_amt > 0): ?>
      <tr>
          <td class="label">Other Charges:</td>
          <td class="amount"><?= number_format($other_charges_amt, 2); ?></td>
      </tr>
      <?php endif; ?>
      <?php if(!empty($tot_discount_to_all_amt) && $tot_discount_to_all_amt != 0): ?>
      <tr>
          <td class="label">Discount:</td>
          <td class="amount">-<?= number_format($tot_discount_to_all_amt, 2); ?></td>
      </tr>
      <?php endif; ?>
      <tr class="grand-total">
          <td class="label">TOTAL:</td>
          <td class="amount"><?= number_format($grand_total, 2); ?></td>
      </tr>
      <?php
      if(change_return_status()) {
          $change_return_amount = get_change_return_amount($sales_id);
          $cash_given = $paid_amount + $change_return_amount;
      ?>
      <tr>
          <td class="label">Cash Given:</td>
          <td class="amount"><?= number_format($cash_given, 2); ?></td>
      </tr>
      <tr>
          <td class="label bold">Change:</td>
          <td class="amount bold"><?= number_format($change_return_amount, 2); ?></td>
      </tr>
      <?php } else { ?>
      <tr>
          <td class="label">Amount Paid:</td>
          <td class="amount"><?= number_format($paid_amount, 2); ?></td>
      </tr>
      <?php if($customer_due > 0): ?>
      <tr>
          <td class="label">Balance Due:</td>
          <td class="amount bold"><?= number_format($customer_due, 2); ?></td>
      </tr>
      <?php endif; ?>
      <?php } ?>
      <?php if($item_discount > 0 || (!empty($tot_discount_to_all_amt) && $tot_discount_to_all_amt != 0)): ?>
      <tr>
          <td class="label" style="font-size:8px;color:#888;">You Saved:</td>
          <td class="amount" style="font-size:8px;color:#888;"><?= number_format($item_discount + $tot_discount_to_all_amt, 2); ?></td>
      </tr>
      <?php endif; ?>
  </table>

  <!-- QR CODE — scans to DU'A Nigeria Instagram -->
  <div class="qr-wrap">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https%3A%2F%2Fwww.instagram.com%2Fdua.nig%3Figsh%3DazhjaXJqbDdudHc1%26utm_source%3Dqr&bgcolor=ffffff&color=000000&margin=4" alt="Scan to follow us on Instagram">
  </div>
  <div class="qr-label">@dua.nig &bull; Follow us on Instagram</div>

  <div class="divider-solid"></div>

  <!-- FOOTER -->
  <div class="footer">
      <div class="no-refund">No Refund After Payment</div>
      <div class="thanks">Thank you for your patronage!</div>
      <div style="margin-top:3px; font-size:8px; color:#888;"><?= $sales_date; ?> &nbsp; <?= $created_time; ?></div>
  </div>

  <script>
  window.onload = function() {
      setTimeout(function() { window.print(); }, 400);
  };
  </script>
  </body>
  </html>
