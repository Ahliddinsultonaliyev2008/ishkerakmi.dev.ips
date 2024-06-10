# register

## HTTP request

```http request
POST http://{{endpoint}}/register
```


## Headers

| Name         | Data             |
|--------------|------------------|
| Content-Type | application/json |



## Request body

| Name                    | Type      | Required | Description           |
|-------------------------|-----------|----------|-----------------------|
| `firstname`             | `string`  | yes      | Firstname             |
| `phone`                 | `numeric` | yes      | Phone number          |
| `password`              | `string`  | yes      | Password              |
| `password_confirmation` | `string`  | yes      | Password confirmation |

`JSON`  

```json
{
    "firstname": "Ali",
    "phone": 901231212,
    "password": "12345678",
    "password_confirmation": "12345678"
}
```

## Response 201
### Foydalanuvchi ro'yxatdan muvafaqiyatli o`tdinggiz

| Name      | Type      | Description      |
|-----------|-----------|------------------|
| `success` | `bool`    | Response status  |
| `massage` | `numeric` | Response massage |
| `user_id` | `numeric` | Created user id  |


`JSON`

```json
{
    "success": true,
    "massage": "Foydalanuvchi ro'yxatdan muvafaqiyatli o`tdinggiz",
    "user_id": 41
}
```
## Response 422
### Content error

| Name      | Type      | Description     |
|-----------|-----------|-----------------|
| `success` | `bool`    | Response status |
| `error`   | `array`   | Response error  |
| `user_id` | `numeric` | Created user id |


`JSON`

```json
{
    "success": false,
    "error": {
        "password": [
            "The password field confirmation does not match.",
            "The password field must be at least 8 characters."
        ]
    }
}
```
`JSON`

```json
{
    "success": true,
    "id": 41,
    "siz royhatdan muafaqiyatli o`tdinggiz": 41
}
```
