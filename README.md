# MultiKeyHashMap
Multi key hashmap for group of entities with more then one unique possible key

This collection has main value, and can have many key maps,
that's mean you can create map (based on this collection) by choosing one of any unique value in collection

for example:

we have some data as

```
| id | slug  | ... other properties |
|  1 | alpha | .................... |
|  2 | beta  | .................... |
```


 we create collection
```php
<?php

    $collection = new Collection(SomeCLass::class, 'id', 'slug');
        foreach ($iterator as $item) {
            $instance = new SomeClass($item); // just example of entity creating
            $collection->push($instance);
        }

    // after that you can get map by passing name of key
    $idMappedCollection = $collection->getMap('id'); // array with ID as key and SomeClass entity as value
    $slugMappedCollection = $collection->getMap('slug'); // array with SLUG as key and SomeClass entity as value
```
