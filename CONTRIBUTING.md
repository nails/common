# How to contribute

We welcome third-party contributions to Nails, in fact we expect it! We set
out to build a tool for ourselves, and hope that we can make other's lives
easier, too.

## Nails Core vs Modules

New functionality is typically wrapped up as a module. We do this to try and
keep the core as lightweight as possible.

We define the Nails core as being the following two repositories:

- `nailsapp/nails`
- `nailsapp/common`

If the functionality you wish to contribute might be considered "optional",
then it should probably be a module.

If you are unsure of whether your contribution should be implemented as a
module or part of Nails Core, [email us](mailto:hello@shedcollective.org)

## Making Changes

We're big fans of the `git flow` model, and use it for this project. We're
flexible with regards acceppting push requests, but pelase adhere to the
following guidelines:

1. Use a git flow feature branch for your patch
2. Use PSR-2 coding style
3. Remove all trailing whitespace
4. Update, and compile, the documentation if necessary