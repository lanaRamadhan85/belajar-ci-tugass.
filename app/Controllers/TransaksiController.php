<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $client;
    protected $apiKey;
    protected $transaction;
    protected $transaction_detail;


    function __construct()
    {
        helper('number');
        helper('form');
        $this->cart = \Config\Services::cart();
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = env('COST_KEY');
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
    }

    public function index()
    {
        $items = $this->cart->contents();
        $total = $this->cart->total();
        // Cek diskon hari ini
        $diskonHariIni = null;
        try {
            $db = db_connect();
            $builder = $db->table('diskon');
            $diskonHariIni = $builder->where('tanggal', date('Y-m-d'))->get()->getRow();
        } catch (\Throwable $e) {}
        if ($diskonHariIni) {
            foreach ($items as &$item) {
                $item['price'] = max(0, $item['price'] - $diskonHariIni->nominal);
            }
            unset($item);
            // Hitung ulang total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'] * $item['qty'];
            }
            session()->set('diskon_nominal', $diskonHariIni->nominal);
        } else {
            session()->remove('diskon_nominal');
        }
        $data['items'] = $items;
        $data['total'] = $total;
        $data['diskon_nominal'] = $diskonHariIni ? $diskonHariIni->nominal : 0;
        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert(array(
            'id'        => $this->request->getPost('id'),
            'qty'       => 1,
            'price'     => $this->request->getPost('harga'),
            'name'      => $this->request->getPost('nama'),
            'options'   => array('foto' => $this->request->getPost('foto'))
        ));
        session()->setflashdata('success', 'Produk berhasil ditambahkan ke keranjang. (<a href="' . base_url() . 'keranjang">Lihat</a>)');
        return redirect()->to(base_url('/'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();
        session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $value) {
            $this->cart->update(array(
                'rowid' => $value['rowid'],
                'qty'   => $this->request->getPost('qty' . $i++)
            ));
        }

        session()->setflashdata('success', 'Keranjang Berhasil Diedit');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);
        session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
        return redirect()->to(base_url('keranjang'));
    }
    public function checkout()
    {
        $items = $this->cart->contents();
        $total = $this->cart->total();
        // Cek diskon hari ini
        $diskonHariIni = null;
        try {
            $db = db_connect();
            $builder = $db->table('diskon');
            $diskonHariIni = $builder->where('tanggal', date('Y-m-d'))->get()->getRow();
        } catch (\Throwable $e) {}
        if ($diskonHariIni) {
            // Kurangi harga per item
            foreach ($items as &$item) {
                $item['price'] = max(0, $item['price'] - $diskonHariIni->nominal);
            }
            unset($item);
            // Hitung ulang total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'] * $item['qty'];
            }
            session()->set('diskon_nominal', $diskonHariIni->nominal);
        } else {
            session()->remove('diskon_nominal');
        }
        $data['items'] = $items;
        $data['total'] = $total;
        $data['diskon_nominal'] = $diskonHariIni ? $diskonHariIni->nominal : 0;
        return view('v_checkout', $data);
    }
    public function getLocation()
    {
        //keyword pencarian yang dikirimkan dari halaman checkout
        $search = $this->request->getGet('search');

        $response = $this->client->request(
            'GET',
            'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=' . $search . '&limit=50',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'key' => $this->apiKey,
                ],
            ]
        );

        $body = json_decode($response->getBody(), true);
        return $this->response->setJSON($body['data']);
    }

    public function getCost()
    {
        //ID lokasi yang dikirimkan dari halaman checkout
        $destination = $this->request->getGet('destination');

        //parameter daerah asal pengiriman, berat produk, dan kurir dibuat statis
        //valuenya => 64999 : PEDURUNGAN TENGAH , 1000 gram, dan JNE
        $response = $this->client->request(
            'POST',
            'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost',
            [
                'multipart' => [
                    [
                        'name' => 'origin',
                        'contents' => '64999'
                    ],
                    [
                        'name' => 'destination',
                        'contents' => $destination
                    ],
                    [
                        'name' => 'weight',
                        'contents' => '1000'
                    ],
                    [
                        'name' => 'courier',
                        'contents' => 'jne'
                    ]
                ],
                'headers' => [
                    'accept' => 'application/json',
                    'key' => $this->apiKey,
                ],
            ]
        );

        $body = json_decode($response->getBody(), true);
        return $this->response->setJSON($body['data']);
    }
    public function buy()
    {
        if ($this->request->getPost()) {
            // Ambil diskon dari session
            $diskon_nominal = session()->get('diskon_nominal') ?? 0;
            $items = $this->cart->contents();
            if ($diskon_nominal) {
                foreach ($items as &$item) {
                    $item['price'] = max(0, $item['price'] - $diskon_nominal);
                }
                unset($item);
            }
            $total_harga = 0;
            foreach ($items as $item) {
                $total_harga += $item['price'] * $item['qty'];
            }
            $dataForm = [
                'username' => $this->request->getPost('username'),
                'total_harga' => $total_harga,
                'alamat' => $this->request->getPost('alamat'),
                'ongkir' => $this->request->getPost('ongkir'),
                'status' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];
            $this->transaction->insert($dataForm);
            $last_insert_id = $this->transaction->getInsertID();
            foreach ($items as $value) {
                $dataFormDetail = [
                    'transaction_id' => $last_insert_id,
                    'product_id' => $value['id'],
                    'jumlah' => $value['qty'],
                    'diskon' => $diskon_nominal,
                    'subtotal_harga' => $value['qty'] * $value['price'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ];
                $this->transaction_detail->insert($dataFormDetail);
            }
            $this->cart->destroy();
            return redirect()->to(base_url());
        }
    }
}