<?php

use yii\db\Migration;
use maerduq\usm\models\MenuItem;
use maerduq\usm\models\Textblock;
use maerduq\usm\models\Redirect;
use maerduq\usm\models\Page;

/**
 * Class m200121_170146_usm_data
 */
class m200121_170146_usm_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('usm_pages', 'content', $this->text()->null());

        $menu = MenuItem::findOne(1);
        $menu->position = 2;
        $menu->save();
        
        $menu = new MenuItem();
		$menu->setAttributes([
			'type' => 'php',
			'visible' => 1,
			'access' => 2,
			'title' => 'Admin',
			'alias' => 'usm',
			'url' => '/usm/default',
			'position'=>1
		]);
        $menu->save();
        
        $menu = new MenuItem();
		$menu->setAttributes([
			'type' => 'php',
			'visible' => 1,
			'access' => 0,
			'title' => 'Projects',
			'alias' => 'projects',
			'url' => '/site/projects',
			'position'=>4
		]);
        $menu->save();
        
        $menu = new MenuItem();
		$menu->setAttributes([
			'type' => 'php',
			'visible' => 1,
			'access' => 0,
			'title' => 'Order',
			'alias' => 'order',
			'url' => '/site/order',
			'position'=>5
		]);
        $menu->save();

        $menu = new MenuItem();
		$menu->setAttributes([
			'type' => 'link',
			'visible' => 1,
			'access' => 0,
			'title' => 'Committee',
			'alias' => 'committee',
			'url' => 'https://etv.tudelft.nl/members/committee/view?id=14',
			'position'=> 3
		]);
        $menu->save();
		
		$menu = new MenuItem();
		$menu->setAttributes([
			'type' => 'empty',
			'visible' => 1,
			'access' => 0,
			'title' => 'Inventory',
			'alias' => 'inventory',
			'position'=>6
		]);
		$menu->save();

        $menu = new MenuItem();
		$menu->setAttributes([
			'type' => 'php',
			'visible' => 1,
			'access' => 0,
			'title' => 'Components',
			'alias' => 'components',
			'url' => '/site/inventory',
			'position'=>1,
			'parent_id' => 6
		]);
        $menu->save();
		
		$menu = new MenuItem();
		$menu->setAttributes([
			'type' => 'php',
			'visible' => 1,
			'access' => 0,
			'title' => 'Devices',
			'alias' => 'devices',
			'url' => '/site/devices',
			'position'=>2,
			'parent_id' => 6
		]);
        $menu->save();

		$tb = new Textblock();
		$tb->setAttributes([
			'name' => 'projects_intro',
			'text' => "<h3>What are these projects?</h3>
<p>These projects have been made by visitors of this site, as such you are able to create your own projects!&nbsp;</p>
<p>&nbsp;</p>
<h3>How can I make a project?</h3>
<p>Flowchart:&nbsp;</p>
<ol>
<li>Document your progress! Use pictures and take notes to show everyone how you made your project.</li>
<li>Write your notes down in markdown format and decide which max. 5 files you'd like to upload and select another picture to use as cover.</li>
<li>Select 'create project'.</li>
<li>Fill in the required fields.</li>
<li>Click 'save file'.</li>
<li>Check your provided email adress and confirm your project.</li>
<li>You can edit the project until it has been checked by one of the members of the klushok committee.</li>
<li>After the project has been checked, it's visible for everyone!</li>
<li>Don't forget to share the project with your friends ;)</li>
</ol>",
			'description' => 'Field above the visible projects',
		]);
		$tb->save();
		
		$tb = new Textblock();
		$tb->setAttributes([
			'name' => 'Device_report_top',
			'text' => '<p>Verify that the device listed below is the device showing problems and please describe the problem as good as possible</p>',
			'description' => 'Text above the report-device form',
		]);
		$tb->save();
		
		$tb = new Textblock();
		$tb->setAttributes([
			'name' => 'Order_heading',
			'text' => "<ol>
<li>Add the components to the shopping cart by filling in the field below and pressing 'add to cart'</li>
<li>When all components have been added to the shopping cart, press 'next'</li>
<li>Fill in you personal details and press 'Order'</li>
<li>An alert notifies you of a succesfull order</li>
<li>Confirm your order by clicking on the link send to the provided email adress</li>
<li>Pay for you order at the ETV desk, there you will be told when the components can be picked up as well.</li>
</ol>",
			'description' => 'Order instructions',
		]);
		$tb->save();

        $tb = new Textblock();
		$tb->setAttributes([
			'name' => 'pricelist-heading',
			'text' => '<p>The availabillity of components is merely an indication: no notifications means a product is shown as available, if a few people notify us of the unavailabillity of a product, the status changes to limited. If multiple persons notify us of missing components, the status changes to sold-out.</p>
<p>If a component is no longer in stock, please let us know <a title="report component link" href="stock">here</a>.</p>
<p>&nbsp;</p>',
			'description' => 'Text above the pricelist',
		]);
		$tb->save();
		
		$redir = new Redirect();
		$redir->setAttributes([
			'active' => 1,
			'url' => 'login',
			'type' => 'php',
			'destination' => '/site/login',
			'forward' => 0,
			'generated' => 0
		]);
		$redir->save();
		
		$redir = new Redirect();
		$redir->setAttributes([
			'active' => 1,
			'url' => 'admin',
			'type' => 'php',
			'destination' => '/site/login',
			'forward' => 1,
			'generated' => 0
		]);
		$redir->save();
		
		$redir = new Redirect();
		$redir->setAttributes([
			'active' => 1,
			'url' => 'devices',
			'type' => 'php',
			'destination' => '/site/devices',
			'forward' => 0,
			'generated' => 0
		]);
		$redir->save();
		
		$page = Page::findOne(1);
		$page->content = '<h1>Welkom to the klushok site!</h1><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. Donec non enim in turpis pulvinar facilisis. Ut felis. Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus</p><h2>Huisregels</h2><p>Ruim je zooi op</p><h2>Contact</h2><p>Heb ja na deze duidelijke uitleg toch nog vragen? stel ze via&nbsp;<a href="mailto:%20klushok-etv@tudelft.nl" data-mce-href="mailto:%20klushok-etv@tudelft.nl">klushok-etv@tudelft.nl</a>&nbsp;of loop een keertje binnen!</p>';
		$page->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200121_170146_usm_data cannot be reverted.\n";

        return false;
    }
}
