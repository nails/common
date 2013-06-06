EXTENDING GENERIC MODELS
========================

Naturally, if a model has no specific home/module you'd assume that placing it here would be the smart thing to do.
Well, you'd be wrong; models placed in here cannot be extended.

By default, a Nails. installation will look here first, followed by third_party, then finally the app.
Nails. models which should be extendable/overrideable should be placed within the appropriate modules
where they can then be extended in the normal way.

Current generic models are stored in system/models.

See the docs for more information

http://docs.nailsapp.co.uk/extending-nails/models