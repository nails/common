# FAQs

## Index

- [How do I query using getAll() effectively?](#how-do-i-query-using-getall-effectively)
- [How do expandable fields work?](#how-do-expandable-fields-work)
- [How do I load assets?](#how-do-i-load-assets)
- [How do I load content from the API?](#how-do-i-load-content-from-the-api)
- [How do I build a CMS template?](#how-do-i-build-a-cms-template)
- [How do I build CMS widgets?](#how-do-i-build-cms-widgets)
- [How do I build admin sections?](#how-do-i-build-admin-sections)
- [How do I add global meta data to pages globally?](#how-do-i-add-global-meta-data-to-pages-globally)
- [How do I upload files to the CDN?](#how-do-i-upload-files-to-the-cdn)
- [How do I serve, crop, and scale files from the CDN?](#how-do-i-serve-crop-and-scale-files-from-the-cdn)
- [How does form validation work?](#how-does-form-validation-work)
- [How do I set flash data for errors/success messages?](#how-do-i-set-flash-data-for-errorssuccess-messages)


## How do I query using getAll() effectively?

See [Querying Models](../intro/factory/models.md#querying-models) for detailed information and examples.


## How do expandable fields work

Expandable fields is a concept in Nails which allows for one model to delegate a query to another model, and bundle the
result into the main response. Expandable fields can be nested to any number of levels.

See [Expandable Fields](../intro/factory/models.md#expandable-fields) for more detailed information, examples, and
diagrams.


## How do I load assets?

External assets (i.e. CSS and JS) should be loaded using the `Asset` service's `load()` method. The `load()` method will
infer the type based on the supplied string's extension. The _second_ (optional) parameter defines where the asset is
loaded from (e.g. *BOWER*), although typically this is not used in the context of the app as other services are
encouraged (i.e. Gulp or WebPack).

If the asset type cannot be inferred from the extension (perhaps there is no extension) then the third parameter can be
specified, this is a string which accepts the values `JS` and `CSS`

```php
$oAsset = Factory::service('Asset');
$oAsset->load('javascript.js');
$oAsset->load('styles.css');
$oAsset->load('http://example.com/styles.css?v=1.2.3', null, 'JS');
```

See [Assets](../intro/assets.md) for more detailed information and examples.


## How do I load content from the API?

    @todo - explain the API controllers and routes

## How do I build a CMS template?

The easiest way to build a CMS template is to use the `nails make:cms:template` command; this will create placeholder
template files in the `application/modules/cms/templates` directory.

See [nails/module-cms docs](https://github.com/nails/module-cms/blob/develop/docs/pages/templates.md) for more
information.


## How do I build CMS widgets

The easiest way to build a CMS widet is to use the `nails make:cms:widget` command; this will create placeholder
widget files in the `application/modules/cms/widgets` directory.

See [nails/module-cms docs](https://github.com/nails/module-cms/blob/develop/docs/widgets/) for more
information.

## How do I build admin sections

    @todo - (including category_id dropdowns from the API)

## How do I add global meta data to pages globally?

This depends on the type of data you wish to add to the page; in any case this should most likely be done in the App's
Base controller (at `src/Controller/Base.php`).

Simple page data can be passed to the views via the `$this->data['page']` variable. This is a `\stdClass` object which
looks like this:

```php
$this->data['page'] = (object) [
    'title' => '',
    'seo'   => (object) [
        'title'       => '',
        'description' => '',
        'keywords'    => '',    
    ]
];
```

Adjusting the above will affect the page's `<title>` tag as well as some basic `<meta>` tags. For more advanced meta
tags, use the `Meta` service.


## How do I upload files to the CDN?

Upload files to the CDN using the `CDN` service's `objectCreate()` method.

    @todo expand on the various ways `objectCreate()` can accept files

## How do I serve, crop, and scale files from the CDN?

Image like objects can be manipulated on-the-fly using the following functions:

| Function                                | Serves                                                                     |
|:----------------------------------------|:---------------------------------------------------------------------------|
| cdnServe($iObjectId, $bForceDownload)   | The unmodified, original file                                              |
| cdnScale($iObjectId, $iWidth, $iHeight) | Resize the image whilst maintaining aspect ratio within maximum boundaries |
| cdnCrop($iObjectId, $iWidth, $iHeight)  | Resize the image to fit the boundaries, cropping here necessary            |
|                                         |                                                                            |

## How does form validation work?

Form validation should use the `FormValidation` service. See
[CodeIgniter's docs](https://www.codeigniter.com/user_guide/libraries/form_validation.html) for further information.

    @todo - expand on this

## How do I set flash data for errors/success messages?

Global feedback messages are used through Nails to signify when something is successful, goes wrong, or simply when
information needs to be presented back to the user. The front end is responsible for how this is rendered.

The "set_flashdata()" method from the `Session` service is used as it will only persists throughout the next page load.
There are four reserved flashdata keys which are checked on page load:

```php
$oSession = Factory::service('Session', 'nails/module-auth');
$oSession->set_flashdata('success', 'Something went right.');
$oSession->set_flashdata('error', 'Something went wrong.');
$oSession->set_flashdata('message', 'This is something you should maybe care about');
$oSession->set_flashdata('info', 'This is purely informational.');
```

Often, setting these keys is immediately followed by a `redirect()` to another page.
