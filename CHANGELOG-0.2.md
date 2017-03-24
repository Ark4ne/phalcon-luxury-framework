#Release Note

## v0.2.0

### Added
- Neutrino\Dotenv. Dotenv loading from `.env.php` file, with configuration of different environment by `.env.{environment}.php`.
- Neutrino\Optimizer. Composer optimizer for Phalcon. Recreate autoloader by using Phalcon\Loader.

- Neutrino\Config\Loader. Configuration loader.
  - `config:cache` command.
  
### Change
- Performance : 
  - Support [ Arr, Str, Obj ] has replaced by functions. `(Arr|Str|Obj):: => (arr|str|obj)_` `Arr:: => arr_` 