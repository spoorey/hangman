#Installation

Make sure your domain points to the `public` directory.

Copy `config/autoload/local.php.dist` to `config/autoload/local.php` and adjust/fill out the database information.

Install dependencies using composer:

`php composer.phar install`

Then build the database using doctrine:

`php public/index.php orm:schema-tool:update --force`

To create an admin user, open the application, open `your-domain.whatever/user/register` and register yourself as a new user.
Setting the `role` in the `hm_game` to "admin" for the user you just created.


Adding words
============
If you want, you can add a suggested list of words using your browsers js console.
This is not good practice, but it works.
- Log in as an admin
- Run the following commands. They simulate sending the "add word" form a ton of times.
- This way, inputfilters are automatically applied (you can of course also add your own words to the json).
- Make sure to leave your browser open until all requests were completed.

```
// replace [] with the contents of data/sample-words.json
var words = [];
// replace hangman.local with your domain
words.forEach(function(word) {
     $.post('http://hangman.local/word/add', {"word": word});
});
```
