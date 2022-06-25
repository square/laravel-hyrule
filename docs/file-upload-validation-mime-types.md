## File Upload Validation - MIME Types

It is considered best practice to validate the MIME type of uploaded files. Here are a few examples on how to do that with Hyrule:

```php
$builder = Hyrule::create()
    ->file('attachment')
        ->mimeType() // Starts MIME-Type contraints
            /*
             * All 5 top-level MIME type categories are supported
             */
            ->application('pdf') // Allows application/pdf
            ->image('jpg', 'png', ...) // Variadic. Enumerate sub-types e.g. image/jpeg, image/png, etc.
            ->video('mp4', 'webm')
            ->multipart(...)
            ->message(...)
            ->end() // Ends MIME Type constraint.
        ->end() // Ends "attachment" field
        // ...
```

Use `->allow(...)` to enumerate specific specific MIME-types:

```php
$builder = Hyrule::create()
    ->array('attachments')
        ->between(1, 10)
        ->each('file')
            ->mimeType()
                ->allow('application/pdf')
                ->allow('image/jpg')
                ->allow('image/png')
                ->allow('image/svg')
                ->allow('video/mp4')
                // etc.
                ->end()
            ->end()
        ->end()
    // ...

```