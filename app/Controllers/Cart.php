<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CartModel;


class Cart extends ResourceController {
    use ResponseTrait;

    private $db;
    
    public function __construct(){
        $this->db = \Config\Database::connect();
    }

    public function addProductInCart(){
        $product_id = $this->request->getVar('product_id');

        $model = new CartModel();

        if($product_id != '' && is_numeric($product_id)){

            $builder = $this->db->table("products");
            $builder->select('*');
            $builder->where('product_id', $product_id);
            $product = $builder->get()->getResult('array');

            if($product != '' && !empty($product)){

                $cartCheck = $model->where('product_id', $product_id)->first();
                
                if(!$cartCheck){

                    $insertData = [
                        'user_id' => $product[0]['user_id'],
                        'product_id' => $product[0]['product_id'],
                        'quantity' => 1
                    ];
    
                    $model->insert($insertData);
    
                    $response = [
                        'status'   => 201,
                        'error'    => null,
                        'messages' => [
                            'success' => 'Product Added to Cart'
                        ]
                    ];
        
                    return $this->respondCreated($response);

                } else {

                    $rowID = $cartCheck['id'];
            
                    $quantityAdd = $cartCheck['quantity'] + 1;

                    $data = [
                        'quantity' => $quantityAdd
                    ];

                    $model->update($rowID, $data);

                    $response = [
                        'status'   => 200,
                        'error'    => null,
                        'messages' => [
                            'success' => 'Product is already in cart so I updated quantity for u'
                        ]
                    ];
        
                    return $this->respond($response);

                }
            } else {

                return $this->failNotFound('Such product Not found in cart');

            }

        } else {
            
            return $this->failValidationError('Received Parameters are incorrect');
        }
    }


    public function removeProductFromCart($product_id = null){
        $product_id = $this->request->getVar('product_id');

        $model = new CartModel();

        if($product_id != '' && is_numeric($product_id)){ 

            $delRow = $model->where('product_id', $product_id)->first();
            
            if($delRow){
                $rowID = $delRow['id'];
                $data = $model->where('id', $rowID)->delete($rowID);
                
                $model->delete($rowID);

                $response = [
                    'status'   => 200,
                    'error'    => null,
                    'messages' => [
                        'success' => 'Product successfully deleted from cart'
                    ]
                ];

                return $this->respondDeleted($response);

            } else {
                return $this->failNotFound('Such product Not found in cart');
            }

        } else {
            return $this->failValidationError('Received Parameters are incorrect');
        }
    }


    public function setCartProductQuantity($product_id = null, $quantity = null){ 
        $product_id = $this->request->getVar('product_id');
        $quantity = $this->request->getVar('quantity');

        $model = new CartModel();

        if($product_id == '' &&  ! ( is_numeric($product_id) ) ){ 
            return $this->failValidationError('Received Parameters are incorrect');
        }

        if($quantity == '' &&  ! ( is_numeric($quantity) ) ){
            return $this->failValidationError('Received Parameters are incorrect');
        }

        $upRow = $model->where('product_id', $product_id)->first();

        if($upRow){
            $rowID = $upRow['id'];
            
            $data = [
                'quantity' => $quantity
            ];

            $model->update($rowID, $data);


            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => [
                    'success' => 'Product Quantity updated successfully'
                ]
            ];

            return $this->respond($response);

        } else {
            return $this->failNotFound('Such product Not found in cart');
        }

    }



    public function getUserCart($id = null){
        $builder = $this->db->table("cart");
        $builder->select('cart.*, p.price as price');
        $builder->join('products as p', 'cart.product_id = p.product_id');
        $builder->orderBy('cart.product_id', 'ASC');
        $cartItems = $builder->get()->getResult('array');

        $responseArray = array();
        $responseArray['products'] = array();

        $cartProductIds = array();
        foreach($cartItems as $singleItem){
            $tmpArray = array(
                'product_id' => $singleItem['product_id'],
                'quantity' => $singleItem['quantity'],
                'price' => $singleItem['price']
            );
            array_push($responseArray['products'], $tmpArray);
            array_push($cartProductIds, $singleItem['product_id']);
        }

        $group_items = $this->db->table("product_group_items as items");
        $group_items->select('items.product_id as product_id, group.discount as discount');
        $group_items->join('user_product_groups as group', 'items.group_id = group.group_id');
        $discountItems = $group_items->get()->getResult('array');

        $group_product_ids = array();
        $discountPercent = 0;
        foreach($discountItems as $item){
            $id = $item['product_id'];
            array_push($group_product_ids, $id);
            $discountPercent = $item['discount'];
        }

        $isDiscount = 0 == count(array_diff($group_product_ids, $cartProductIds));

        if($isDiscount){
            $tempArray = array();
            foreach($responseArray['products'] as $discProd){
                if(in_array($discProd['product_id'], $group_product_ids)){
                    array_push($tempArray, $discProd);
                }
            }

            $min = array_reduce($tempArray, function($min, $properties){
                return min($min, $properties['quantity']);
            }, PHP_INT_MAX);

            $totalDiscount = 0;
            foreach($tempArray as $rame){
                $price = $rame['price'];

                $discount = ( $discountPercent / 100 ) * $price;

                $totalDiscountSingle = $discount * $min;

                $totalDiscount += $totalDiscountSingle;
            }
        }

        $responseArray['discount'] = $totalDiscount;

        return $this->respond($responseArray);
    }

}