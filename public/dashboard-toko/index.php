<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard - TOKO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: white;
      font-family: 'Segoe UI', sans-serif;
    }
    .main-container {
      max-width: 900px;
      margin: 50px auto;
    }
    h1 {
      font-weight: 600;
      margin-bottom: 10px;
    }
    .subtext {
      font-size: 1rem;
      color: #555;
    }
    table {
      margin-top: 30px;
    }
    table thead {
      background-color: #f5f5f5;
    }
    table th, table td {
      vertical-align: middle;
      border: none;
    }
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: #fafafa;
    }
    .alert {
      margin-bottom: 20px;
    }
    .text-muted {
      color: #6c757d !important;
    }
    .summary-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .summary-number {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .summary-label {
      font-size: 1rem;
      opacity: 0.9;
    }
  </style>
</head>
<body>
<?php 
// Fungsi untuk mengambil data dari API
function curl() {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://localhost:8080/api",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/x-www-form-urlencoded",
            "key: random123678abcghi",
        ),
    ));
    $output = curl_exec($curl);
    curl_close($curl);
    return json_decode($output);
}

$send1 = curl();
$hasil1 = $send1->results ?? [];

// Debug: Tampilkan struktur data untuk debugging dan troubleshooting
if (empty($hasil1)) {
    echo '<div class="alert alert-warning">Tidak ada data dari API atau API tidak berespon</div>';
} else {
    // Debug: Tampilkan struktur data pertama untuk melihat field yang tersedia
    if (isset($hasil1[0])) {
        echo '<div class="alert alert-info" id="debug-info">
                <strong>Debug Info:</strong> Struktur data dari API - ' . json_encode($hasil1[0]) . '
                <button type="button" class="btn-close float-end" onclick="document.getElementById(\'debug-info\').style.display=\'none\'"></button>
              </div>';
    }
}

// Hitung total item yang dibeli
$total_items = 0;
$total_transactions = 0;
$total_revenue = 0;

if (!empty($hasil1)) {
    foreach ($hasil1 as $item1) {
        $total_transactions++;
        $total_revenue += isset($item1->total_harga) ? $item1->total_harga : 0;
        
        // Hitung jumlah item dari berbagai kemungkinan field
        $jumlah_item = null;
        if (isset($item1->jumlah_item)) {
            $jumlah_item = $item1->jumlah_item;
        } elseif (isset($item1->total_item)) {
            $jumlah_item = $item1->total_item;
        } elseif (isset($item1->item_count)) {
            $jumlah_item = $item1->item_count;
        } elseif (isset($item1->qty)) {
            $jumlah_item = $item1->qty;
        } elseif (isset($item1->quantity)) {
            $jumlah_item = $item1->quantity;
        }
        
        // Jika tidak ada field jumlah item langsung, coba hitung dari details (API)
        if ($jumlah_item === null && isset($item1->details) && is_array($item1->details)) {
            $jumlah_item = 0;
            foreach ($item1->details as $d) {
                $jumlah_item += isset($d->jumlah) ? $d->jumlah : 0;
            }
        }
        
        // Tambahkan ke total jika ada jumlah item
        if ($jumlah_item !== null && $jumlah_item > 0) {
            $total_items += $jumlah_item;
        }
    }
}
?>

<div class="main-container">
  <div class="text-center">
    <h1>Dashboard - TOKO</h1>
    <p class="subtext"><?= date("l, d-m-Y") ?> <span id="jam"></span>:<span id="menit"></span>:<span id="detik"></span></p>
  </div>

  <table class="table table-bordered table-striped text-center">

    <thead>
      <tr>
        <th>No</th>
        <th>Username</th>
        <th>Alamat</th>
        <th>Total Harga</th>
        <th>Jumlah Item</th>
        <th>Ongkir</th>
        <th>Status</th>
        <th>Tanggal Transaksi</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $i = 1;
      if (!empty($hasil1)):
        foreach ($hasil1 as $item1): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($item1->username) ?></td>
            <td><?= htmlspecialchars($item1->alamat) ?></td>
            <td><?= number_format($item1->total_harga) ?></td>
            <td>
              <?php 
              // Cek berbagai kemungkinan nama field untuk jumlah item
              // Mencoba field yang umum digunakan untuk jumlah item
              $jumlah_item = null;
              if (isset($item1->jumlah_item)) {
                  $jumlah_item = $item1->jumlah_item;
              } elseif (isset($item1->total_item)) {
                  $jumlah_item = $item1->total_item;
              } elseif (isset($item1->item_count)) {
                  $jumlah_item = $item1->item_count;
              } elseif (isset($item1->qty)) {
                  $jumlah_item = $item1->qty;
              } elseif (isset($item1->quantity)) {
                  $jumlah_item = $item1->quantity;
              }
              
              // Jika tidak ada field jumlah item langsung, coba hitung dari details (API)
              if ($jumlah_item === null && isset($item1->details) && is_array($item1->details)) {
                  $jumlah_item = 0;
                  foreach ($item1->details as $d) {
                      $jumlah_item += isset($d->jumlah) ? $d->jumlah : 0;
                  }
              }
              
              // Tampilkan jumlah item jika ada dan valid
              if ($jumlah_item !== null && $jumlah_item > 0) {
                  echo $jumlah_item . ' item';
              } else {
                  echo '<span class="text-muted">-</span>';
              }
              ?>
            </td>
            <td><?= number_format($item1->ongkir) ?></td>
            <td>
              <?= $item1->status == 0 ? '0' : '1' ?>
            </td>
            <td><?= $item1->created_at ?></td>
          </tr>
        <?php endforeach;
      else: ?>
        <tr><td colspan="8">Tidak ada data transaksi.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
function waktu() {
    const waktu = new Date();
    document.getElementById("jam").innerText = String(waktu.getHours()).padStart(2, '0');
    document.getElementById("menit").innerText = String(waktu.getMinutes()).padStart(2, '0');
    document.getElementById("detik").innerText = String(waktu.getSeconds()).padStart(2, '0');
    setTimeout(waktu, 1000);
}
waktu();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>