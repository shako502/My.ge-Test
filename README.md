# My.ge დავალება
## დავალების დეტალები


შექმენით php ფაილი (შეგიძლიათ გამოიყენოთ ნებისმიერი framework) სადაც გექნებათ შემდეგი
endpoint - ები;

- POST addProductInCart - პროდუქტის კალათში დამატება. მიიღებს product_id - ს

- POST removeProductFromCart - კალათიდან პროდუქტის წაშლა. მიიღებს product_id - ს

- POST setCartProductQuantity - კალათში პროდუქტის რაოდენობის ცვლილება. მიიღებს
product_id - ს და quantity - ს

- GET getUserCart - დააბრუნებს მომხმარებლის კალათაში არსებულ პროდუქტებს შემდეგი
ფორმატით.
```javascript
{
    products : [
        {product_id: 1, quantity:1, price:10},
        {product_id: 2, quantity:3, price:15},
        {product_id: 5, quantity:2, price:20},
        ...
    ],
    discount: 10.5 // ჯამური ფასდაკლება ერთეულებში (არა პროცენტული)
}
```
***

## შესრულებული დეტალები

- კოდი დაწერილია PHP-ის Framework-ზე ( CodeIgniter ). 
- შეიქმნა API ენდფოინთები :
> /addproductincart - პროდუქტის კალათაში დამატებისთვის
> /removeproductfromcart - პროდუქტის კალათიდან წაშლისთვის
> /setcartproductquantity - პროდუქტის რაოდენობის ცვლილებისთვის
> /getusercart - მომხმარებლის კალათის მისაღებად


***

## კონფიგურაცია

კონფიგურაციის ფაილის მისამართი: //App/Config/App.php  - Line 26
ვებ-გვერდის (აპლიკაციის) მთავარი მისამართის შესაცვლელად:
```php
public $baseURL = 'http://myge.test/index.php';
```

მონაცემთა ბაზის კონფიგურაციის ფაილის მისამართი: //App/Config/Database.php - Line 33
```php
public $default = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'myge',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];
	```

