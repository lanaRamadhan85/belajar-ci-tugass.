<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>

<div id="alert-success" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
  Data berhasil ditambahkan.
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDiskon" id="btnTambahDiskon">Tambah Data</button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="tabelDiskon">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Nominal (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($diskon as $d): ?>
                        <tr data-id="<?= $d['id'] ?>">
                            <td><?= $no++ ?></td>
                            <td class="td-tanggal"><?= $d['tanggal'] ?></td>
                            <td class="td-nominal"><?= number_format($d['nominal'], 0, ',', '.') ?></td>
                            <td>
                                <button class="btn btn-success btn-sm btn-edit" data-id="<?= $d['id'] ?>">Ubah</button>
                                <button class="btn btn-danger btn-sm btn-hapus" data-id="<?= $d['id'] ?>">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah/Edit Diskon -->
<div class="modal fade" id="modalDiskon" tabindex="-1" aria-labelledby="modalDiskonLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formDiskon">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title" id="modalDiskonLabel">Tambah Diskon</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="diskon_id">
        <div class="mb-3">
          <label for="tanggal" class="form-label">Tanggal</label>
          <input type="date" class="form-control" id="tanggal" name="tanggal" required>
          <div class="invalid-feedback" id="error-tanggal"></div>
        </div>
        <div class="mb-3">
          <label for="nominal" class="form-label">Nominal</label>
          <input type="number" class="form-control" id="nominal" name="nominal" required>
          <div class="invalid-feedback" id="error-nominal"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Hapus Diskon -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formHapusDiskon">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus data diskon ini?</p>
        <input type="hidden" name="id" id="hapus_id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#tabelDiskon').DataTable({
        "lengthMenu": [5, 10, 25, 50, 100],
        "pageLength": 10,
        "language": {
            "lengthMenu": "_MENU_ entries per page",
            "search": "Cari:",
            "paginate": {
                "previous": "Sebelumnya",
                "next": "Berikutnya"
            }
        }
    });
    // Tambah Data
    $('#btnTambahDiskon').on('click', function() {
        $('#modalDiskonLabel').text('Tambah Diskon');
        $('#formDiskon')[0].reset();
        $('#diskon_id').val('');
        $('#tanggal').prop('readonly', false);
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
    });

    // Edit Data
    $('.btn-edit').on('click', function() {
        var id = $(this).data('id');
        $.get('<?= base_url('diskon/get/') ?>' + id, function(data) {
            $('#modalDiskonLabel').text('Edit Diskon');
            $('#diskon_id').val(data.id);
            $('#tanggal').val(data.tanggal).prop('readonly', true);
            $('#nominal').val(data.nominal);
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');
            $('#modalDiskon').modal('show');
        });
    });

    // Submit Tambah/Edit
    $('#formDiskon').on('submit', function(e) {
        e.preventDefault();
        var id = $('#diskon_id').val();
        var url = id ? '<?= base_url('diskon/update/') ?>' + id : '<?= base_url('diskon/store') ?>';
        var formData = $(this).serialize();
        $.post(url, formData, function(res) {
            if(res.status == 'success') {
                if(!id) {
                  $('#alert-success').fadeIn();
                  $('#modalDiskon').modal('hide');
                } else {
                  location.reload();
                }
            } else {
                if(res.errors) {
                    if(res.errors.tanggal) {
                        $('#tanggal').addClass('is-invalid');
                        $('#error-tanggal').text(res.errors.tanggal);
                    }
                    if(res.errors.nominal) {
                        $('#nominal').addClass('is-invalid');
                        $('#error-nominal').text(res.errors.nominal);
                    }
                }
            }
        }, 'json');
    });

    // Hapus Data
    $('.btn-hapus').on('click', function() {
        $('#hapus_id').val($(this).data('id'));
        $('#modalHapus').modal('show');
    });

    $('#formHapusDiskon').on('submit', function(e) {
        e.preventDefault();
        var id = $('#hapus_id').val();
        $.get('<?= base_url('diskon/delete/') ?>' + id, function(res) {
            if(res.status == 'success') {
                location.reload();
            }
        }, 'json');
    });
});
</script>
<?= $this->endSection() ?> 