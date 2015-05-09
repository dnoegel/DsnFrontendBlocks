<?php

namespace Shopware\DsnFrontendBlocks\Components;

/**
 * BlockSplitter splits a given template string into smarty blocks.
 *
 * @package Shopware\DsnFrontendBlocks\Components
 */
class BlockSplitter
{
    const BLOCK_START = '(?P<open>{\s*block\s*name\s*=\s*["\']?(?P<name>.+?)["\']?\s*})';
    const BLOCK_END = '(?P<close>{/block})';


    /**
     * Split $template into smary blocks and return an array with info about all the blocks
     *
     * @param $template
     * @return array
     */
    public function split($template)
    {
        $matches = array();
        preg_match_all('#'. self::BLOCK_START. '|' . self::BLOCK_END . '#', $template, $matches, PREG_OFFSET_CAPTURE);

        $open = array();
        $result = array();

        // Iterate all matches and build the result array
        foreach ($matches[0] as $key => $match) {
            $value = $match[0];
            $offset = $match[1];
            $name = $matches['name'][$key][0];
            $isStart = $matches['name'][$key][1] > -1;

            if ($isStart) {
                // iterate all currently open blocks and increase the child counter
                foreach ($open as &$parentElement) {
                    $parentElement['children'] += 1;
                }
                // push the open block to the stack
                $open[] = array('name' => $name, 'start' => $offset, 'startContent' => $offset + strlen($value), 'children' => 0);
            } else {
                // remove the closed block from `open` stack, collect some info and push it to `close` stack
                $element = array_pop($open);
                $element['endContent'] = $offset;
                $element['end'] = $offset + strlen($value);
                $element['content'] = substr($template, $element['start'], $element['end'] - $element['start']);
                $element['contentOnly'] = substr($template, $element['startContent'], $element['endContent'] - $element['startContent']);
                $element['beginBlock'] = substr($template, $element['start'], $element['startContent'] - $element['start']);
                $element['endBlock'] = substr($template, $element['endContent'], $element['end'] - $element['endContent']);

                $result[] = $element;
            }
        }

        // sort by children - the replaccement later will go from the deepest child towards the main parent
        usort($result, function($a, $b) {
            $a = $a['children'];
            $b = $b['children'];

            if ($a == $b) {
                return 0;
            };

            return $a < $b ? 1 : -1;

        });

        return $result;

    }
}