<?php

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

    public function testBlockParser()
    {
        $parser = new \Shopware\DsnFrontendBlocks\Components\BlockSplitter();
        $result = $parser->split($this->template);

        foreach (array('test2', 'test3', 'test5', 'test4', 'test') as $key => $value) {
            $this->assertEquals($value, $result[$key]['name']);
        }
    }

    public function testAnnotator()
    {
        $annotator = new \Shopware\DsnFrontendBlocks\Components\BlockAnnotator();
        $template = $annotator->annotate($this->template, 'test');
        
    }

}