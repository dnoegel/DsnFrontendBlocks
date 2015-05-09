<?php

namespace Shopware\DsnFrontendBlocks\Components;

/**
 * BlockAnnotator annotates smarty block with HTML comments, so you can tell which content belongs to which block
 *
 * @package Shopware\DsnFrontendBlocks\Components
 */
class BlockAnnotator
{
    /**
     * @var BlockSplitter
     */
    private $blockSplitter;

    public function __construct(BlockSplitter $blockSplitter)
    {

        $this->blockSplitter = $blockSplitter;
    }

    /**
     * Do not append block info to blacklisted blocks (e.g. JS, CSS)
     * @var array
     */
    protected $blacklist = array(
//        'frontend_index_start',
//       'frontend_index_doctype',
//       'frontend_index_html',
//       'frontend_index_header',
//       'frontend_index_no_script_message',
//       'frontend_index_header_meta_http_tags',
//       'frontend_index_header_meta_tags',
//       'frontend_index_header_meta_robots',
//       'frontend_index_header_meta_keywords',
//       'frontend_index_header_meta_description',
//       'frontend_index_header_meta_tags_schema_webpage',
//       'frontend_index_header_favicons',
//       'frontend_index_header_meta_tags_ie9',
//       'frontend_index_header_meta_tags_android',
//       'frontend_index_header_canonical',
//       'frontend_index_header_feeds',
         'frontend_index_header_title',
//       'frontend_index_header_css_screen',
//       'frontend_index_header_css_print',
//       'frontend_index_header_javascript_modernizr_lib',
//       'frontend_index_header_css_ie',
//       'frontend_index_header_javascript_jquery_lib',
//       'frontend_index_header_javascript',
//       'frontend_index_header_javascript_inline',
//       'frontend_index_header_javascript_jquery',
    );


    public function annotate($template)
    {

        foreach ($this->blockSplitter->split($template) as $block) {

            if (in_array($block['name'], $this->blacklist)) {
                continue;
            }

            $info = $block['name'];
            $start = "<!-- BLOCK BEGIN {$info} -->";
            $end = "<!-- BLOCK END {$info} -->";

            $template = str_replace($block['content'], $block['beginBlock'] . $start . $block['contentOnly']. $end . $block['endBlock'], $template);

        }

        return $template;
    }

}