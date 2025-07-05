<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DiskonModel;

class DiskonController extends BaseController
{
    protected $diskonModel;

    public function __construct()
    {
        $this->diskonModel = new DiskonModel();
    }

    public function index()
    {
        $data['diskon'] = $this->diskonModel->orderBy('tanggal', 'asc')->findAll();
        return view('v_diskon', $data);
    }

    public function create() {}

    public function store()
    {
        $rules = [
            'tanggal' => 'required|is_unique[diskon.tanggal]',
            'nominal' => 'required|numeric',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
        }
        $this->diskonModel->insert([
            'tanggal' => $this->request->getPost('tanggal'),
            'nominal' => $this->request->getPost('nominal'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function edit($id) {}

    public function update($id)
    {
        $rules = [
            'nominal' => 'required|numeric',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
        }
        $this->diskonModel->update($id, [
            'nominal' => $this->request->getPost('nominal'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function delete($id)
    {
        $this->diskonModel->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function get($id)
    {
        $diskon = $this->diskonModel->find($id);
        return $this->response->setJSON($diskon);
    }
} 