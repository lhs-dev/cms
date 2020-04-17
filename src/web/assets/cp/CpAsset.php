<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\web\assets\cp;

use Craft;
use craft\base\ElementInterface;
use craft\config\GeneralConfig;
use craft\elements\User;
use craft\helpers\Assets;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use craft\i18n\Locale;
use craft\models\Section;
use craft\services\Sites;
use craft\web\AssetBundle;
use craft\web\assets\axios\AxiosAsset;
use craft\web\assets\d3\D3Asset;
use craft\web\assets\datepickeri18n\DatepickerI18nAsset;
use craft\web\assets\elementresizedetector\ElementResizeDetectorAsset;
use craft\web\assets\fabric\FabricAsset;
use craft\web\assets\fileupload\FileUploadAsset;
use craft\web\assets\garnish\GarnishAsset;
use craft\web\assets\iframeresizer\IframeResizerAsset;
use craft\web\assets\jquerypayment\JqueryPaymentAsset;
use craft\web\assets\jquerytouchevents\JqueryTouchEventsAsset;
use craft\web\assets\jqueryui\JqueryUiAsset;
use craft\web\assets\picturefill\PicturefillAsset;
use craft\web\assets\selectize\SelectizeAsset;
use craft\web\assets\velocity\VelocityAsset;
use craft\web\assets\xregexp\XregexpAsset;
use craft\web\View;
use yii\web\JqueryAsset;

/**
 * Asset bundle for the control panel
 */
class CpAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->depends = [
            AxiosAsset::class,
            D3Asset::class,
            ElementResizeDetectorAsset::class,
            GarnishAsset::class,
            JqueryAsset::class,
            JqueryTouchEventsAsset::class,
            JqueryUiAsset::class,
            JqueryPaymentAsset::class,
            DatepickerI18nAsset::class,
            PicturefillAsset::class,
            SelectizeAsset::class,
            VelocityAsset::class,
            FileUploadAsset::class,
            XregexpAsset::class,
            FabricAsset::class,
            IframeResizerAsset::class,
        ];

        $this->css = [
            'css/craft.css',
            'css/charts.css',
        ];

        $this->js[] = 'js/Craft' . $this->dotJs();

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        if ($view instanceof View) {
            $this->_registerTranslations($view);
        }

        // Define the Craft object
        $craftJson = Json::encode($this->_craftData(), JSON_UNESCAPED_UNICODE);
        $js = <<<JS
window.Craft = {$craftJson};
JS;
        $view->registerJs($js, View::POS_HEAD);
    }

    private function _registerTranslations(View $view)
    {
        $view->registerTranslations('app', [
            '(blank)',
            'A server error occurred.',
            'Actions',
            'All',
            'Any changes will be lost if you leave this page.',
            'Apply this to the {number} remaining conflicts?',
            'Are you sure you want to delete this draft?',
            'Are you sure you want to delete this image?',
            'Are you sure you want to delete “{name}”?',
            'Are you sure you want to transfer your license to this domain?',
            'Buy {name}',
            'Cancel',
            'Choose a user',
            'Switching sites will lose unsaved changes. Are you sure you want to switch sites?',
            'Choose which table columns should be visible for this source, and in which order.',
            'Close Preview',
            'Close',
            'Continue',
            'Copied to clipboard.',
            'Copy the reference tag',
            'Copy the URL',
            'Copy to clipboard',
            'Couldn’t delete “{name}”.',
            'Couldn’t save new order.',
            'Create',
            'Delete folder',
            'Delete heading',
            'Delete it',
            'Delete them',
            'Delete user',
            'Delete users',
            'Delete',
            'Display as thumbnails',
            'Display in a table',
            'Done',
            'Draft Name',
            'Drafts',
            'Edit draft settings',
            'Edit',
            'Element',
            'Elements',
            'Enabled everywhere',
            'Enabled for {site}',
            'Enter the name of the folder',
            'Enter your password to continue.',
            'Enter your password to log back in.',
            'Export Type',
            'Export',
            'Export…',
            'Failed',
            'Format',
            'From {date}',
            'From',
            'Give your tab a name.',
            'Handle',
            'Heading',
            'Hide sidebar',
            'Hide',
            'Incorrect password.',
            'Instructions',
            'Keep both',
            'Keep me logged in',
            'Keep them',
            'License transferred.',
            'Limit',
            'Log out now',
            'Login',
            'Make not required',
            'Make required',
            'Merge the folder (any conflicting files will be replaced)',
            'More',
            'Move',
            'Name',
            'New category',
            'New child',
            'New entry',
            'New heading',
            'New order saved.',
            'New position saved.',
            'New subfolder',
            'New {group} category',
            'New {section} entry',
            'Next Page',
            'No limit',
            'Notes',
            'OK',
            'Options',
            'Password',
            'Past year',
            'Past {num} days',
            'Pay {price}',
            'Pending',
            'Previous Page',
            'Really delete folder “{folder}”?',
            'Remove',
            'Rename folder',
            'Rename',
            'Reorder',
            'Replace it',
            'Replace the folder (all existing files will be deleted)',
            'Save as a new asset',
            'Save',
            'Saving',
            'Score',
            'Search in subfolders',
            'Select transform',
            'Select',
            'Settings',
            'Show nav',
            'Show sidebar',
            'Show',
            'Show/hide children',
            'Sort by {attribute}',
            'Source settings saved',
            'Structure',
            'Submit',
            'Table Columns',
            'The draft could not be saved.',
            'The draft has been saved.',
            'This can be left blank if you just want an unlabeled separator.',
            'This month',
            'This week',
            'This year',
            'To {date}',
            'To',
            'Today',
            'Transfer it to:',
            'Try again',
            'Update {type}',
            'Upload failed for {filename}',
            'Upload files',
            'What do you want to do with their content?',
            'What do you want to do?',
            'Your session has ended.',
            'Your session will expire in {time}.',
            'by {creator}',
            'day',
            'days',
            'hour',
            'hours',
            'minute',
            'minutes',
            'saved {timestamp} by {creator}',
            'second',
            'seconds',
            'updated {timestamp}',
            'week',
            'weeks',
            '{ctrl}C to copy.',
            '{first, number}-{last, number} of {total, number} {total, plural, =1{{item}} other{{items}}}',
            '{first}-{last} of {total}',
            '{num, number} {num, plural, =1{Available Update} other{Available Updates}}',
            '{total, number} {total, plural, =1{{item}} other{{items}}}',
            '{type} saved.',
            '“{name}” deleted.',
        ]);
    }

    private function _craftData(): array
    {
        $upToDate = Craft::$app->getIsInstalled() && !Craft::$app->getUpdates()->getIsCraftDbMigrationNeeded();
        $request = Craft::$app->getRequest();
        $generalConfig = Craft::$app->getConfig()->getGeneral();
        $sitesService = Craft::$app->getSites();
        $locale = Craft::$app->getLocale();
        $orientation = $locale->getOrientation();
        $userSession = Craft::$app->getUser();
        $currentUser = $userSession->getIdentity();
        $primarySite = $upToDate ? $sitesService->getPrimarySite() : null;
        $view = Craft::$app->getView();

        $elementTypeNames = [];
        foreach (Craft::$app->getElements()->getAllElementTypes() as $elementType) {
            /** @var string|ElementInterface $elementType */
            $elementTypeNames[$elementType] = [
                $elementType::displayName(),
                $elementType::pluralDisplayName(),
                $elementType::lowerDisplayName(),
                $elementType::pluralLowerDisplayName(),
            ];
        }

        $data = [
            'actionTrigger' => $generalConfig->actionTrigger,
            'actionUrl' => UrlHelper::actionUrl(),
            'allowUppercaseInSlug' => (bool)$generalConfig->allowUppercaseInSlug,
            'apiParams' => Craft::$app->apiParams,
            'asciiCharMap' => StringHelper::asciiCharMap(true, Craft::$app->language),
            'baseApiUrl' => Craft::$app->baseApiUrl,
            'baseCpUrl' => UrlHelper::cpUrl(),
            'baseSiteUrl' => UrlHelper::siteUrl(),
            'baseUrl' => UrlHelper::url(),
            'canAccessQueueManager' => $userSession->checkPermission('utility:queue-manager'),
            'cpTrigger' => $generalConfig->cpTrigger,
            'datepickerOptions' => $this->_datepickerOptions($locale, $currentUser, $generalConfig),
            'defaultIndexCriteria' => ['enabledForSite' => null],
            'deltaNames' => $view->getDeltaNames(),
            'editableCategoryGroups' => $upToDate ? $this->_editableCategoryGroups() : [],
            'edition' => Craft::$app->getEdition(),
            'elementTypeNames' => $elementTypeNames,
            'fileKinds' => Assets::getFileKinds(),
            'initialDeltaValues' => $view->getInitialDeltaValue(),
            'isImagick' => Craft::$app->getImages()->getIsImagick(),
            'isMultiSite' => Craft::$app->getIsMultiSite(),
            'language' => Craft::$app->language,
            'left' => $orientation === 'ltr' ? 'left' : 'right',
            'limitAutoSlugsToAscii' => (bool)$generalConfig->limitAutoSlugsToAscii,
            'maxUploadSize' => Assets::getMaxUploadSize(),
            'modifiedDeltaNames' => $request->getBodyParam('modifiedDeltaNames', []),
            'omitScriptNameInUrls' => (bool)$generalConfig->omitScriptNameInUrls,
            'orientation' => $orientation,
            'pageNum' => $request->getPageNum(),
            'pageTrigger' => $generalConfig->getPageTrigger(),
            'path' => $request->getPathInfo(),
            'pathParam' => $generalConfig->pathParam,
            'primarySiteId' => $primarySite ? (int)$primarySite->id : null,
            'primarySiteLanguage' => $primarySite->language ?? null,
            'Pro' => Craft::Pro,
            'publishableSections' => $upToDate && $currentUser ? $this->_publishableSections($currentUser) : [],
            'registeredAssetBundles' => ['' => ''], // force encode as JS object
            'registeredJsFiles' => ['' => ''], // force encode as JS object
            'remainingSessionTime' => !in_array($request->getSegment(1), ['updates', 'manualupdate'], true) ? $userSession->getRemainingSessionTime() : 0,
            'right' => $orientation === 'ltr' ? 'right' : 'left',
            'runQueueAutomatically' => (bool)$generalConfig->runQueueAutomatically,
            'scriptName' => $request->getScriptFile(),
            'siteId' => $upToDate ? (int)$sitesService->currentSite->id : null,
            'sites' => $this->_sites($sitesService),
            'siteToken' => $generalConfig->siteToken,
            'slugWordSeparator' => $generalConfig->slugWordSeparator,
            'Solo' => Craft::Solo,
            'systemUid' => Craft::$app->getSystemUid(),
            'timepickerOptions' => $this->_timepickerOptions($locale, $orientation),
            'timezone' => Craft::$app->getTimeZone(),
            'tokenParam' => $generalConfig->tokenParam,
            'translations' => ['' => ''], // force encode as JS object
            'useCompressedJs' => (bool)$generalConfig->useCompressedJs,
            'usePathInfo' => (bool)$generalConfig->usePathInfo,
            'username' => $currentUser->username ?? null,
        ];

        if ($generalConfig->enableCsrfProtection) {
            $data['csrfTokenName'] = $request->csrfParam;
            $data['csrfTokenValue'] = $request->getCsrfToken();
        }

        return $data;
    }

    private function _datepickerOptions(Locale $locale, User $currentUser = null, GeneralConfig $generalConfig): array
    {
        return [
            'constrainInput' => false,
            'dateFormat' => $locale->getDateFormat(Locale::LENGTH_SHORT, Locale::FORMAT_JUI),
            'dayNames' => $locale->getWeekDayNames(Locale::LENGTH_FULL),
            'dayNamesMin' => $locale->getWeekDayNames(Locale::LENGTH_ABBREVIATED),
            'dayNamesShort' => $locale->getWeekDayNames(Locale::LENGTH_SHORT),
            'firstDay' => (int)(($currentUser ? $currentUser->getPreference('weekStartDay') : null) ?? $generalConfig->defaultWeekStartDay),
            'monthNames' => $locale->getMonthNames(Locale::LENGTH_FULL),
            'monthNamesShort' => $locale->getMonthNames(Locale::LENGTH_ABBREVIATED),
            'nextText' => Craft::t('app', 'Next'),
            'prevText' => Craft::t('app', 'Prev'),
        ];
    }

    private function _editableCategoryGroups(): array
    {
        $groups = [];

        foreach (Craft::$app->getCategories()->getEditableGroups() as $group) {
            $groups[] = [
                'handle' => $group->handle,
                'id' => (int)$group->id,
                'name' => Craft::t('site', $group->name),
                'uid' => Craft::t('site', $group->uid),
            ];
        }

        return $groups;
    }

    private function _publishableSections(User $currentUser): array
    {
        $sections = [];

        foreach (Craft::$app->getSections()->getEditableSections() as $section) {
            if ($section->type !== Section::TYPE_SINGLE && $currentUser->can("createEntries:$section->uid")) {
                $sections[] = [
                    'entryTypes' => $this->_entryTypes($section),
                    'handle' => $section->handle,
                    'id' => (int)$section->id,
                    'name' => Craft::t('site', $section->name),
                    'sites' => $section->getSiteIds(),
                    'type' => $section->type,
                    'uid' => $section->uid,
                    'canPublish' => $currentUser->can("publishEntries:$section->uid"),
                ];
            }
        }

        return $sections;
    }

    private function _entryTypes(Section $section): array
    {
        $types = [];

        foreach ($section->getEntryTypes() as $type) {
            $types[] = [
                'handle' => $type->handle,
                'id' => (int)$type->id,
                'name' => Craft::t('site', $type->name),
            ];
        }

        return $types;
    }

    private function _sites(Sites $sitesService): array
    {
        $sites = [];

        foreach ($sitesService->getAllSites() as $site) {
            $sites[] = [
                'handle' => $site->handle,
                'id' => (int)$site->id,
                'uid' => (string)$site->uid,
                'name' => Craft::t('site', $site->name),
            ];
        }

        return $sites;
    }

    private function _timepickerOptions(Locale $locale, string $orientation): array
    {
        return [
            'closeOnWindowScroll' => false,
            'lang' => [
                'AM' => $locale->getAMName(),
                'am' => mb_strtolower($locale->getAMName()),
                'PM' => $locale->getPMName(),
                'pm' => mb_strtolower($locale->getPMName()),
            ],
            'orientation' => $orientation[0],
            'timeFormat' => $locale->getTimeFormat(Locale::LENGTH_SHORT, Locale::FORMAT_PHP),
        ];
    }
}
