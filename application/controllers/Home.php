<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Home extends CI_Controller {

    private $userData;

	public function __construct() {
        parent::__construct();

        $this->userData = $this->session->userData(); //cuva podatke u sesiji
    }


	public function index($category_id = 0) {
        $this->load->model('Item_model', 'model');
        $viewData = [];

        /* search */
        $search = $this->input->get('search');
        $start = (int)$this->input->get('per_page');
        
        $where = [];
        if($search) {
            $where['title LIKE'] = '%'. $search .'%';
        }
        /* end search */

        if($category_id) {
            $where['category_id'] = (int)$category_id;
        }

        $viewData['items'] = $this->model->result('id,title,price,image', $where, $start);

        /* pagination */
        $this->pagination->initialize([
            'base_url' => base_url() . ($category_id ? 'category/'.$category_id : '') . ($search ? '?search=' . $search : ''),
            'total_rows' => $this->model->get_count($where)
        ]);
        $viewData['pagination'] = $this->pagination->create_links();
        /* end pagination */

		$this->render('home', $viewData);
	}


    public function add_cart($item_id) {
    	if(!isset($this->userData['logged'])) {
    		$this->add_alert('warning', 'You must login firstly.');
    		redirect(base_url('login'));
		} else {
            $this->load->model('Item_model');

			$item = $this->Item_model->row($item_id);
			if(!is_object($item)) {
				show_404();
			}
			$this->userData['cart'][] = $item_id;
			$this->session->set_userdata('cart', $this->userData['cart']);
			$this->add_alert('success', 'Product successful added to cart');
			redirect(base_url('cart'));
		}
	}

	public function cart() {
		if(!isset($this->userData['logged'])) {
			redirect(base_url('login'));
		}

		$delete = $this->input->get('del');
		if($delete) {
			unset($this->userData['cart'][$delete-1]);
			$this->session->set_userdata('cart', $this->userData['cart']);
			$this->add_alert('success', 'Successful deleted.');
			redirect(base_url('cart'));
		}

        $this->load->model('Item_model');
		$data = ['total' => 0];
		foreach($this->userData['cart'] as $key=>$item_id) {
			$item = $this->Item_model->row($item_id);
			$data['items'][$key] = $item;
			$data['total'] += $item->price;
		}

		$this->render('cart', $data);
	}


	public function checkout() {
    	if(!isset($this->userData['cart']) || !is_array($this->userData['cart'])) {
    		$this->add_alert('warning', 'You cart is empty.');
    		redirect('/');
		}

        $this->load->model('Item_model');
		$this->load->model('Order_model');
        $this->load->model('OrderItem_model');
        $this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation
			->set_rules('firstname', 'First name', 'trim|required')
			->set_rules('lastname', 'Last name', 'trim|required')
			->set_rules('address', 'Address', 'trim|required')
			->set_rules('address_e', 'Address 2', 'trim')
			->set_rules('country', 'Country', 'trim|required')
			->set_rules('state', 'State', 'trim|required')
			->set_rules('zip', 'Zip', 'trim|required')
			->set_rules('paymentmethod', 'Payment method', 'trim|required');

		$data = ['total' => 0];
		foreach($this->userData['cart'] as $key=>$item_id) {
			$item = $this->Item_model->row($item_id);
			$data['items'][$key] = $item;
			$data['total'] += $item->price;
		}

		if($this->form_validation->run() == TRUE) {
			$orderData = [
				'user_id' => $this->userData['user_id'],
				'first_name' => $this->input->post('firstname'),
				'last_name' => $this->input->post('lastname'),
				'address' => $this->input->post('address'),
				'address_e' => $this->input->post('address_e'),
				'country' => $this->input->post('country'),
				'state' => $this->input->post('state'),
				'zip' => $this->input->post('zip'),
				'payment' => $this->input->post('paymentmethod'),
				'price' => $data['total']
			];
            $order_id = $this->Order_model->insert_data($orderData);
			if($order_id) {
				foreach($data['items'] as $item) {
					$this->OrderItem_model->insert([
						'order_id' => $order_id,
						'item_id' => $item->id,
						'title' => $item->title,
						'price' => $item->price
					]);
				}
				$this->userData['cart'] = [];
				$this->session->set_userdata('cart', $this->userData['cart']);
				$this->add_alert('success', 'You order successful confirmed.');
			    redirect(base_url('orders'));
			} else {
				$this->add_alert('danger', 'Error on system. Please contact administrator.');
			}
		}

		$data['user'] = $this->userData;
		$data['country'] = json_decode(file_get_contents('./assets/country.json'), true);
    	$this->render('checkout', $data);
	}


	public function orders() {
		if(!isset($this->userData['logged'])) {
			redirect(base_url('login'));
		}

        $this->load->model('Order_model', 'model');
		$start = (int)$this->input->get('per_page');
        $where = ['user_id' => $this->userData['user_id']];

		$this->pagination->initialize([
			'base_url' => base_url('orders'),
			'total_rows' => $this->model->get_count($where)
		]);

		$data = [
			'items' => $this->model->result('*', $where),
			'pagination' => $this->pagination->create_links()
		];

		$this->render('orders', $data);
	}

    /* for Paypal */
    function purchaces() {
        if(!isset($this->userData['logged'])) {
			redirect(base_url('login'));
		}


        $this->render('purchaces');
    }
    /* end For Paypal */


    public function order_detail($order_id) {
        //check order for logged user
        $this->load->model('Order_model', 'model');

        $where = ['user_id' => $this->userData['user_id'], 'id' => $order_id];
        $order = $this->model->row($where);
        if(!$order) {
            show_404();
        }
        $items = $this->model->get_items($order_id);

        $data = [
            'order' => $order,
            'items' => $items
        ];

        // load only container
        $this->load->view('order_detail', $data);
    }


    public function logout() {
        $this->session->unset_userdata([
            'logged', 'user_id', 'email', 'first_name', 'last_name'
        ]);
        redirect(base_url());
    }


    public function login() {
        
        if(isset($this->userData['logged'])) {
            redirect(base_url());
        }

        $viewData = [];

        $this->load->model('User_model', 'model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation
                ->set_rules('email', 'Email', 'required|trim|valid_email')
                ->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run()) {
            $user = [
                'email' => $this->input->post('email'),
                'password' => md5(sha1($this->input->post('password')))
            ];
            $userData = $this->model->row($user, 'id, email, first_name, last_name, level');

            if(is_object($userData)) {
                $newdata = [
                    'logged' => true,
                    'user_id' => $userData->id,
                    'email' => $userData->email,
                    'level' => $userData->level,
                    'first_name' => $userData->first_name,
                    'last_name' => $userData->last_name
                ];
                $newdata['cart'] = isset($this->userData['cart']) ? $this->userData['cart'] : [];
                $this->session->set_userdata($newdata);
                redirect(base_url());
            } else {
                $viewData['error'] = 'Login or password incorrect.';
            }
        }
        
        $this->render('login', $viewData);
    }


    public function register() {
        if(isset($this->userData['logged'])) {
            redirect(base_url());
        }

        $viewData = [];

        $this->load->model('User_model', 'model');
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation
                ->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]')
                ->set_rules('first_name', 'First name', 'required|trim|min_length[2]|max_length[50]')
                ->set_rules('last_name', 'Last name', 'required|trim|min_length[2]|max_length[50]')
                ->set_rules('password', 'Password', 'required|trim|min_length[4]|max_length[50]')
                ->set_rules('passconf', 'Password Confirm', 'required|trim|matches[password]');

        if ($this->form_validation->run()) {
            $data = [
                'email' => $this->input->post('email'),
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'password' => md5(sha1($this->input->post('password')))
            ];
            $insert = $this->model->insert($data); //upis u bazu
            if($insert) {
                $newdata = [
                    'logged' => true,
                    'level' => 0,
                    'user_id' => $insert,
                    'email' => $data['email'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name']
                ];
                $newdata['cart'] = isset($this->userData['cart']) ? $this->userData['cart'] : [];
                $this->session->set_userdata($newdata);
                $viewData['success'] = true;
            }
        }

        $this->render('register', $viewData);
    }


    public function page($page_id) {
        $this->load->model('Page_model', 'model');
        $page = $this->model->row($page_id);
        if(!$page) {
            show_404();
        }

        $this->render('page', $page);
    }


    private function add_alert($type, $message) {
        $alert = ['type' => $type, 'message' => $message];
        $this->session->set_flashdata('alert', $alert);
    }


    private function render($page, $data = []) {
        /* category */
        $categories = $this->Category_model->result('*');
        /* end category */

        
        $headerData = [
            'categories' => $categories,
            'user' => $this->userData,
            'alert' => $this->session->flashdata('alert')
        ];

        $this->load->view('inc/header', $headerData);
        $this->load->view($page, $data);
        $this->load->view('inc/footer');
    }


}
