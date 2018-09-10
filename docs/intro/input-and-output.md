# Input & Output

- [Input](#input)
- [Output](#output)


## Input

The `Input` service is responsible for data which comes as part of the request, e.g. `$_POST`, `$_GET`, `$_SERVER`, etc.

The following methods are available, and are fairly self-explanatory:

```
$oInput = Factory::service('Input');

//  The following methods, if no $sKey is provided, will return the entire array
$oInput->post($sKey);
$oInput->get($sKey);
$oInput->server($sKey);
```

For more information see [the CodeIgniter docs](https://www.codeigniter.com/user_guide/libraries/input.html).

## Output

The Output class is largely automatic and sits in the background; you will primarily send stuff to the browser using the
`View` service. However, in some circumstances, this can be useful.

```
$oOutput = Factory::service('Output');
```

For more information see [the CodeIgniter docs](https://www.codeigniter.com/user_guide/libraries/output.html).
