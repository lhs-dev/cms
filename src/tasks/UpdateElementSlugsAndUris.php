<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\app\tasks;

use Craft;
use craft\app\base\Element;
use craft\app\base\ElementInterface;
use craft\app\base\Task;

/**
 * UpdateElementSlugsAndUris represents an Update Element Slugs and URIs background task.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class UpdateElementSlugsAndUris extends Task
{
    // Properties
    // =========================================================================

    /**
     * @var integer|integer[] The ID(s) of the element(s) to update
     */
    public $elementId;

    /**
     * @var string|ElementInterface The type of elements to update.
     */
    public $elementType;

    /**
     * @var string The locale of the elements to update.
     */
    public $locale;

    /**
     * @var boolean Whether the elements’ other locales should be updated as well.
     */
    public $updateOtherLocales = true;

    /**
     * @var boolean Whether the elements’ descendants should be updated as well.
     */
    public $updateDescendants = true;

    /**
     * @var
     */
    private $_elementIds;

    /**
     * @var
     */
    private $_skipRemainingEntries;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getTotalSteps()
    {
        $this->_elementIds = (array)$this->elementId;
        $this->_skipRemainingEntries = false;

        return count($this->_elementIds);
    }

    /**
     * @inheritdoc
     */
    public function runStep($step)
    {
        if ($this->_skipRemainingEntries) {
            return true;
        }

        $elementsService = Craft::$app->getElements();
        /** @var Element $element */
        $element = $elementsService->getElementById($this->_elementIds[$step], $this->elementType, $this->locale);

        // Make sure they haven't deleted this element
        if (!$element) {
            return true;
        }

        $oldSlug = $element->slug;
        $oldUri = $element->uri;

        $elementsService->updateElementSlugAndUri($element, $this->updateOtherLocales, false, false);

        // Only go deeper if something just changed
        if ($this->updateDescendants && ($element->slug !== $oldSlug || $element->uri !== $oldUri)) {
            /** @var Element $elementType */
            $elementType = $this->elementType;

            $childIds = $elementType::find()
                ->descendantOf($element)
                ->descendantDist(1)
                ->status(null)
                ->localeEnabled(null)
                ->locale($element->locale)
                ->ids();

            if ($childIds) {
                $this->runSubTask([
                    'type' => static::className(),
                    'description' => Craft::t('app', 'Updating children'),
                    'elementId' => $childIds,
                    'elementType' => $this->elementType,
                    'locale' => $this->locale,
                    'updateOtherLocales' => $this->updateOtherLocales,
                    'updateDescendants' => true,
                ]);
            }
        } else if ($step === 0) {
            // Don't bother updating the other entries
            $this->_skipRemainingEntries = true;
        }

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function getDefaultDescription()
    {
        return Craft::t('app', 'Updating element slugs and URIs');
    }
}
