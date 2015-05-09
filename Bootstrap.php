<?php

class Shopware_Plugins_Frontend_DsnFrontendBlocks_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getVersion() {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'plugin.json'), true);
        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    public function getLabel()
    {
        return 'DsnFrontendBlocks';
    }

    public function install()
    {
        if (!$this->assertVersionGreaterThen('4.3.0')) {
            throw new \RuntimeException('At least Shopware 4.3.0 is required');
        }

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            'onStartDispatch'
        );

        return true;
    }

    /**
     * This callback function is triggered at the very beginning of the dispatch process and allows
     * us to register additional events on the fly. This way you won't ever need to reinstall you
     * plugin for new events - any event and hook can simply be registerend in the event subscribers
     */
    public function onStartDispatch(Enlight_Event_EventArgs $args)
    {
        $this->registerMyComponents();
                

        $subscribers = array(
            new \Shopware\DsnFrontendBlocks\Subscriber\Frontend($this)

        );

        foreach ($subscribers as $subscriber) {
            $this->Application()->Events()->addSubscriber($subscriber);
        }
    }

    public function registerMyComponents()
    {
        $this->Application()->Loader()->registerNamespace(
            'Shopware\DsnFrontendBlocks',
            $this->Path()
        );
    }
}