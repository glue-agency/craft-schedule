<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule;

use Craft;
use craft\base\Model;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\services\UserPermissions;
use craft\web\Response;
use craft\web\twig\variables\CraftVariable;
use GlueAgency\schedule\console\SchedulesController;
use GlueAgency\schedule\models\Settings;
use GlueAgency\schedule\plugin\Routes;
use GlueAgency\schedule\plugin\Services;
use GlueAgency\schedule\user\Permissions;
use GlueAgency\schedule\web\twig\CraftVariableBehavior;
use yii\base\Event;
use yii\base\InvalidRouteException;
use yii\console\Application;

/**
 * Class Plugin
 *
 * @package GlueAgency\schedule
 * @method Settings getSettings()
 * @property-read Settings $settings
 * @author Glue Agency <info@glue.be>
 */
class Plugin extends \craft\base\Plugin
{
    // Traits
    // =========================================================================

    use Routes;
    use Services;

    // Properties
    // =========================================================================

    /**
     * @var Plugin
     */
    public static Plugin $plugin;

    /**
     * @var string
     */
    public string $schemaVersion = '0.4.0';

    /**
     * @var ?string
     */
    public ?string $t9nCategory = 'schedule';

    /**
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * @var bool
     */
    public bool $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * Init.
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;
        Craft::setAlias('@schedule', $this->getBasePath());

        if (strval($this->getSettings()->customName) !== "") {
            $this->name = $this->getSettings()->customName;
        } else {
            $this->name = Craft::t('schedule', 'Schedule');
        }

        // Replace omnilight controller to this plugin controller in console.
        if (Craft::$app instanceof Application) {
            if (isset(Craft::$app->controllerMap['schedule']) && Craft::$app->controllerMap['schedule'] == 'omnilight\scheduling\ScheduleController') {
                unset(Craft::$app->controllerMap['schedule']);
                Craft::$app->controllerMap['schedules'] = SchedulesController::class;
            }
        }

        $this->_setComponents();
        $this->_registerCpRoutes();
        $this->_registerUserPermissions();
        $this->_registerVariables();
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): ?array
    {
        $ret = parent::getCpNavItem();

        if (strval($this->getSettings()->customCpNavName)) {
            $ret['label'] = $this->getSettings()->customCpNavName;
        } else {
            $ret['label'] = $this->name;
        }

        $user = Craft::$app->getUser();
        if ($user->checkPermission(Permissions::MANAGE_SCHEDULES)) {
            $ret['subnav']['schedules'] = [
                'label' => Craft::t('schedule', 'Schedules'),
                'url' => 'schedule',
            ];
        }

        if ($user->checkPermission(Permissions::MANAGE_LOGS)) {
            $ret['subnav']['logs'] = [
                'label' => Craft::t('schedule', 'Logs'),
                'url' => 'schedule/logs',
            ];
        }

        if (Craft::$app->getConfig()->getGeneral()->allowAdminChanges && $user->getIsAdmin()) {
            $ret['subnav']['settings'] = [
                'label' => Craft::t('schedule', 'Settings'),
                'url' => 'schedule/settings',
            ];
        }

        return $ret;
    }

    /**
     * @inheritdoc
     * @throws InvalidRouteException
     */
    public function getSettingsResponse(): Response
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('schedule/settings'));
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return Model|null
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    // Private Methods
    // =========================================================================

    /**
     * Register user permissions.
     */
    private function _registerUserPermissions(): void
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions[] = [
                'heading' => Craft::t('schedule', 'Schedules'),
                'permissions' => [
                    Permissions::MANAGE_SCHEDULES => [
                        'label' => Craft::t('schedule', 'Manage Schedules'),
                    ],
                    Permissions::MANAGE_LOGS => [
                        'label' => Craft::t('schedule', 'Manage Logs'),
                    ],
                ]
            ];
        });
    }

    /**
     * Register the plugin template variable.
     */
    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->attachBehavior('schedule', CraftVariableBehavior::class);
        });
    }
}
