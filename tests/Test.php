<?php
use Shopware\DsnFrontendBlocks\Components\BlockAnnotator;
use Shopware\DsnFrontendBlocks\Components\BlockSplitter;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class PluginTest extends Shopware\Components\Test\Plugin\TestCase
{
    protected static $ensureLoadedPlugins = array(
        'DsnFrontendBlocks' => array()
    );

    protected $template = <<<'EOF'
{block name=test}{/block}

{block name='test2'}
    2
    {block name='test3'}
        3
        {block name='test4'}
            4
        {/block}
    {/block}
{/block}

{block name="test5"}5{/block}
EOF;

    protected $templateResult = <<<'EOF'
{block name=test}<!-- BLOCK BEGIN test --><!-- BLOCK END test -->{/block}

{block name='test2'}<!-- BLOCK BEGIN test2 -->
    2
    {block name='test3'}<!-- BLOCK BEGIN test3 -->
        3
        {block name='test4'}<!-- BLOCK BEGIN test4 -->
            4
        <!-- BLOCK END test4 -->{/block}
    <!-- BLOCK END test3 -->{/block}
<!-- BLOCK END test2 -->{/block}

{block name="test5"}<!-- BLOCK BEGIN test5 -->5<!-- BLOCK END test5 -->{/block}
EOF;

    public function testBlockSplitter()
    {
        $parser = new BlockSplitter();
        $result = $parser->split($this->template);

        foreach (array('test2', 'test3', 'test5', 'test4', 'test') as $key => $value) {
            $this->assertEquals($value, $result[$key]['name']);
        }
    }

    public function testAnnotator()
    {
        $annotator = new BlockAnnotator(
            new BlockSplitter()
        );
        $template = $annotator->annotate($this->template, 'test');
        $this->assertEquals($this->templateResult, $template);
    }

}