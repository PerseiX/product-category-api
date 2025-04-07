# How to run?

Run `make install`. When you want to stop your application please run `make stop`

# Getting products

```GET http://localhost:8080/api/products```

# Getting one product

```GET http://localhost:8080/api/products/{productId}```

# Add new product

```
POST http://localhost:8080/api/products
Content-Type: application/json

{
    "price": "10.23",
    "name": "Product 1",
    "currency": "PLN",
    "categories": [1]
}
```

# Update product

```
PUT http://localhost:8080/api/products/814f0996-1810-4000-98e8-0181df308b6f
Content-Type: application/json

{
    "price": "10.23",
    "name": "New product name",
    "currency": "PLN",
    "categories": [3]
}
```

# Delete product

```DELETE http://localhost:8080/api/products/814f0996-1810-4000-98e8-0181df308b6f```
