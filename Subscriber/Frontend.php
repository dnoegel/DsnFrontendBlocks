<?php

namespace Shopware\DsnFrontendBlocks\Subscriber;

use Shopware\DsnFrontendBlocks\Components\BlockAnnotator;
use Shopware\DsnFrontendBlocks\Components\BlockSplitter;

class Frontend implements \Enlight\Event\SubscriberInterface
{
    /**
     * @var BlockAnnotator
     */
    protected $annotator;

    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PreDispatch_Frontend' => 'onPreDispatch',
            'Enlight_Controller_Action_PreDispatch_Widgets' => 'onPreDispatch',
        );
    }

    /**
     * PreDispatch callback for widget and frontend requests
     *
     * @param \Enlight_Event_EventArgs $args
     */
    public function onPreDispatch(\Enlight_Event_EventArgs $args)
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

    /**
     * Smarty preFilter callback. Modify template and return
     *
     * @param $source
     * @param $template
     * @return mixed
     */
    public function preFilter($source, $template)
    {
        return $this->annotator->annotate($source);
    }

    /**
     * Set own template directory
     *
     * @param $templateManager
     */
    private function reconfigureTemplateDirs(\Enlight_Template_Manager $templateManager)
    {
        $compileDir = $templateManager->getCompileDir() . 'blocks/';
        $cacheDir = $templateManager->getTemplateDir() . 'blocks/';

        $templateManager->setCompileDir($compileDir);
        $templateManager->setCacheDir($cacheDir);
    }
}