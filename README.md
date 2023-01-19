Race condition workarounds for concurrent [`*OrCreate`](https://laravel.com/docs/eloquent#upserts) method calls.

# Usage

Apply the `CanRunOrCreateConcurrently` trait to a model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kekalainen\EloquentConcurrency\CanRunOrCreateConcurrently;

abstract class BaseModel extends Model
{
    use CanRunOrCreateConcurrently;	
}
```

# Limitations

- Only MySQL and MariaDB are supported.
- [Relationship methods](https://laravel.com/docs/eloquent-relationships#the-create-method) are not handled.
