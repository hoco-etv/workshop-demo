# Content Management System **[USM](http://www.depaul.nl/projecten/usm)** for Yii2
Published under MIT license.

## Getting started
* `composer require maerduq/usm` - Download USM
* `yii migrate --migrationPath=@vendor/maerduq/usm/src/migrations --interactive=0` - Update databases

### Add to your config.php to get it all up and running:
~~~
  'modules' => [
    'usm' => [
      'class' => 'maerduq\usm\UsmModule',
      <configuration here>
    ],
  ],
  'components' => [
    'urlManager' => [
      'rules' => [
        ['class' => 'maerduq\usm\components\RedirectRule'],
        ...
      ],
      ...
    ]
  ],
  'components' => [
    'formatter' => [
      'class' => 'maerduq\usm\components\Formatter'
    ],
    ...
~~~

## Migrate from manual module setup
Before this was a composer package, you could install USM by installing it manually in your Yii modules folder from [this repository](https://bitbucket.org/maerduq/usm-yii2). That repository will be discontinued, the latest version is `v1.1.0`. Migration from that repository to this one is very easy, since the versioning continues. Switching this repository is therefor as easy as 1, 2, 3:

1. Delete the `/modules/usm` folder from your project
2. Import the new style USM by executing the command `$ composer require maerduq/usm:~1.1.0`
3. Search and replace every occurence of `app\modules\usm\` in your code to `maerduq\usm\` to update the namespaces

## Configuration _(under construction)_
- access_type: type of access control to be used for the backend (possible: **usm** or **yii**)
- access_check_admin: when _access_type=**yii**_, the default check for being an admin is to check whether someone is logged in. If a custom check is wanted, implement a callback here. When _true_ is returned, the action is allowed
- access_password: the password to be used when using _access_type=**usm**_
- layout_container: the full layout for pages to be placed in
- layout_plain: the layout where more advanced pages should be placed in
- layout_empty: a layout for pages which do not need layout
- languages : array of the languages used in the application. First array item should be equal to the default language (Yii::$app->language)
- sitemaps: array of controllers containing a sitemap() method, returning an array of sitemap entries. The array item 'url' is required and should contain the input for the Url::to() function.
- plugins: array of controllers to be included in the backend. These controllers should extend UsmController

## Layout hooks
- use `Usm::getMenu()` for the menu as to be presented for the user
- use `Usm::getBreadcrumbs()` to get the breadcrumbs for the current page. Set $this->view->params['breadcrumbs'] in a controller to hook into this
- use `Usm::getBreadcrumbs($url, [$lastWithLink = false])` to get the breadcrumbs for a specific page. Usefull to build breadcrumbs for detail pages, of which the overview page is in the menu.
- use `$this->params['pageHeader']` for the page title

## Controller hooks
- use `Usm::isUserAdmin()` to check whether a user is admin. Example (to restrict the access to SiteController@test)
- use `Usm::evalContent($text)` to parse all CMS page hooks in a static text
~~~
class SiteController extends Controller {

  public function behaviors() {
    return parent::behaviours() + [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          ['allow' => Usm::isUserAdmin()],
          ['allow' => true]
        ]
      ]
    ];
  }
  
  ...
}
~~~
- assign `$this->view->params['breadcrumbs']` to hook into Usm::breadcrumbs()
- assign `$this->view->params['pageHeader']` to give the page a title

## CMS page hooks
* {{baseUrl}} will point to the web root folder;
* use the path alias `@usm` to reach USM files

## Textblocks
 * usage: <?= \maerduq\usm\models\Textblock::read(<name>, [<replaces>]) ?>
 * <name> will always be unique, category is only for better back-end
 * <name> can also be an array of names of textblocks. This can be used if multiple textblocks are used in one page, to avoid multiple queries. it will return an array indexed on textblock name.
* <replaces> is an optional array to use to fill the Textblock template
