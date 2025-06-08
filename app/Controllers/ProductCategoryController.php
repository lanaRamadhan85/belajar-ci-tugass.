<?php

namespace App\Controllers;

use App\Models\ProductCategoryModel;

class ProductCategoryController extends BaseController
{
    protected $productkategori;

    public function __construct()
    {
        $this->productkategori = new ProductCategoryModel();
    }

    public function index()
    {
        $productkategori = $this->productkategori->findAll();
        $data['productkategori'] = $productkategori;

        return view('v_product_category', $data);
    }

    public function create()
    {
        $dataForm = [
            'category_name' => $this->request->getPost('nama'),
            'created_at' => date("Y-m-d H:i:s")
        ];

        $this->productkategori->insert($dataForm);

        return redirect()->to('produk_kategori')->with('success', 'Data Berhasil Ditambah');
    }

    public function edit($id)
    {
        $dataForm = [
            'category_name' => $this->request->getPost('nama'),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        $this->productkategori->update($id, $dataForm);

        return redirect()->to('produk_kategori')->with('success', 'Data Berhasil Diubah');
    }

    public function delete($id)
    {
        $this->productkategori->delete($id);

        return redirect()->to('produk_kategori')->with('success', 'Data Berhasil Dihapus');
    }
}
