# Change Log


## Unreleased

- **Validation no longer depends on CodeIgniter.** `Service\FormValidation` and `Factory\Service\FormValidation\Validator` now run on a standalone engine (`Nails\Common\Validation\*`), so validation works from the console and in tests. `CodeIgniter\Libraries\FormValidation` has been removed.
- Rules are classes implementing `Nails\Common\Interfaces\Validation\Rule`, auto-discovered from every component's `Validation\Rule` namespace (app rules override module rules, which override common's). Rules may be referenced by name (`required`, `max_length[5]`), by class name, as an instance, or as a closure. Custom rule methods on `NAILS_Form_validation` / `App\Common\Service\FormValidation` must be ported to `App\Validation\Rule\*`.
- Closures receive `(mixed $mValue, Nails\Common\Validation\Context $oContext)`; use `$oContext->getValue('other_field')` for cross-field checks (replaces reading `validation_data`). A closure returning `false` now fails.
- Rules run in declared order, with `required`/`isset` hoisted first (CodeIgniter ran callbacks first).
- Cross-field rules (`matches`, `differs`, `date_before`, ...) read the data being validated rather than `$_POST`.
- Unknown rules throw `Nails\Common\Validation\Exception\UnknownRuleException` instead of silently failing the field; error messages are never blank (every rule carries a default).
- New `Nails\Common\Service\Translation` (`Factory::service('Translation')`) loads `*_lang.php` files without CodeIgniter and is now the sole source for `lang()`. `CodeIgniter\Core\Lang` is removed: **delete `application/core/NAILS_Lang.php` from your app** (it extends the removed class and will fatal on boot); replace `get_instance()->lang->load('x')` with `Factory::service('Translation')->load('x')` (or nothing: lines are loaded lazily on first use). Language files from every component are loaded eagerly, app last, so a key resolves the same way on every request.
- The CodeIgniter-style methods on `Service\FormValidation` (`set_rules`, `set_message`, `set_data`, `run`, `error_array`, `set_value`, ...) still work but are deprecated; `set_value()`, `form_error()`, `set_select()`, `set_radio()`, `set_checkbox()` and `validation_errors()` are now provided by Nails and read from the last validation run.
- `Validator` gains `setLabels()`, `setFieldMessages()`, `getResult()` and `getValidatedData()` (the data with rule mutations such as `trim` applied); `getErrors()` no longer reads shared state.
- `Validator` can be extended: override `rules()` (and optionally `messages()`, `labels()`, `fieldMessages()`) to define a reusable, unit-testable rule set with its dependencies in the constructor; runtime `setRules()`/`addRules()`/`setMessages()`/... merge over the class-defined values. `stubRule($sName, $mRule)` replaces a named rule for one validator (e.g. stub `is_unique` in a test), and `setEngine()` swaps the engine entirely.
- The `cdnObjectPickerMulti*` rules have moved to `nails/module-cdn`.
- Fixed: `date_today` never matched; `date_before` always failed; `maxWords` registered its message under `max_words`; several `fv_*` language lines had typos.

## Version 0.2.3

Release date: 21st June 2014

- Removed dependencies, modules should depend on this not the other way around.


## Version 0.2.2

Release date: 21st June 2014

- First release where modules are (almost) independent packages


## Version 0.2.1

Release date: 20th June 2014

- Bug fix; looking for old package name


## Version 0.2.0

Release date: 20th June 2014

- Second release. Lot's changed, to much to mention.
- Publishing everything through GitHub now, future releases will be a little mroe organised.


## Version 0.1.0

Release date: 23rd September 2013

- Initial Release, first release through composer, not intended to be stable at all.