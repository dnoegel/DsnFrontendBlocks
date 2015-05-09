<?php

namespace Shopware\DsnFrontendBlocks\Subscriber;

use Shopware\DsnFrontendBlocks\Components\BlockAnnotator;
use Shopware\DsnFrontendBlocks\Components\BlockSplitter;

class Frontend implements \Enlight\Event\SubscriberInterface
{
    protected $annotator;

    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onFrontendPostDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onFrontendPostDispatch',
        );
    }

    public function onFrontendPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();

        if (!$controller->Request()->getCookie('blocks')) {
            return;
        }

        $this->annotator = new BlockAnnotator(new BlockSplitter());

        // set own caching dirs
        $this->reconfigureTemplateDirs($view->Engine());

        // configure shopware to not strip HTML comments
        Shopware()->Config()->offsetSet('sSEOREMOVECOMMENTS', false);

        $view->Engine()->registerFilter('pre', array($this, 'preFilter'));
    }

    public function preFilter($source, \Smarty $template)
    {
        return $this->annotator->annotate($source);
    }

    /**
     * @param $templateManager
     */
    private function reconfigureTemplateDirs($templateManager)
    {
        $compileDir = $templateManager->getCompileDir() . 'blocks/';
        $cacheDir = $templateManager->getTemplateDir() . 'blocks/';

        $templateManager->setCompileDir($compileDir);
        $templateManager->setCacheDir($cacheDir);
    }
}