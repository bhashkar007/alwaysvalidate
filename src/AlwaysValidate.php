<?php
/**
 * AlwaysValidate plugin for Craft CMS 3.x
 *
 * Make Craft validate disabled elements before saving
 *
 * @link      http://sidd3.com
 * @copyright Copyright (c) 2018 Bhashkar Yadav
 */

namespace by\alwaysvalidate;

use by\alwaysvalidate\services\AlwaysValidateService as AlwaysValidateServiceService;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\base\Element;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\services\Elements;
use craft\events\ElementEvent;

use yii\base\Event;

/**
 * Class AlwaysValidate
 *
 * @author    Bhashkar Yadav
 * @package   AlwaysValidate
 * @since     1.0.0
 *
 * @property  AlwaysValidateServiceService $alwaysValidateService
 */
class AlwaysValidate extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var AlwaysValidate
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Event::on(Entry::class, Entry::EVENT_BEFORE_SAVE, function (ModelEvent $event) {
            $entry = $event->sender;
            // set the scenario to live
            $entry->setScenario(Element::SCENARIO_LIVE);

            // validate the model
            $entry->validate();

            // get the errors,
            if(!$entry->enabled && $entry->validate())
            {   
                $errors = $entry->getErrors();
                $event->isValid = false;
            }
            return $event;
        });

        Craft::info(
            Craft::t(
                'always-validate',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
