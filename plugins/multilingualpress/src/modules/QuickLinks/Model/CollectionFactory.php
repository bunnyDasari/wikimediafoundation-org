<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\QuickLinks\Model;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Language\Bcp47Tag;
use Inpsyde\MultilingualPress\Framework\NetworkState;
use Inpsyde\MultilingualPress\Framework\Url\SimpleUrl;
use function Inpsyde\MultilingualPress\siteLocaleName;
use InvalidArgumentException;

/**
 * Class CollectionFactory
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
 */
class CollectionFactory
{
    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var SiteSettingsRepository
     */
    private $siteSettingsRepository;

    /**
     * FactoryCollection constructor.
     * @param ContentRelations $contentRelations
     * @param SiteSettingsRepository $siteSettingsRepository
     */
    public function __construct(
        ContentRelations $contentRelations,
        SiteSettingsRepository $siteSettingsRepository
    ) {

        $this->contentRelations = $contentRelations;
        $this->siteSettingsRepository = $siteSettingsRepository;
    }

    /**
     * Create the Model Collection
     *
     * All of the models within the collection are related with the given site and content id.
     *
     * @param int $sourceSiteId
     * @param int $sourceContentId
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function create(int $sourceSiteId, int $sourceContentId): Collection
    {
        $emptyCollection = new Collection([]);

        try {
            $relations = $this->contentRelations->relations(
                $sourceSiteId,
                $sourceContentId,
                'post'
            );
        } catch (NonexistentTable $exc) {
            return $emptyCollection;
        }

        unset($relations[$sourceSiteId]);

        if (!$relations) {
            return $emptyCollection;
        }

        return $this->buildModelCollectionByContentRelations($relations);
    }

    /**
     * Build the Collection of Models by the Given Content Relations
     *
     * Content Relations is an array where the keys are the site id and the value the content id.
     *
     * @param array $contentRelations
     * @return Collection
     * @throws InvalidArgumentException
     */
    protected function buildModelCollectionByContentRelations(array $contentRelations): Collection
    {
        $models = [];
        $networkState = $this->networkState();

        foreach ($contentRelations as $remoteSiteId => $remoteContentId) {
            switch_to_blog($remoteSiteId);
            try {
                $currentModel = $this->singleModel($remoteSiteId, $remoteContentId);
            } catch (NonexistentTable $exc) {
                continue;
            } catch (InvalidArgumentException $exc) {
                continue;
            }

            $models[$remoteSiteId] = $currentModel;
        }
        $networkState->restore();

        return new Collection($models);
    }

    /**
     * Create the Single Model
     *
     * The returned value is an array like this one
     *
     * ```
     * [
     *     'url' => URL OF THE TARGET POST,
     *     'language' => HTTP LANGUAGE CODE OF THE TARGET SITE
     *     'label' => THE TEXT TO USE AS LABEL FOR THE ITEM
     * ]
     * ```
     *
     * @param int $remoteSiteId
     * @param int $remoteContentId
     * @return ModelInterface
     * @throws InvalidArgumentException
     * @throws NonexistentTable
     */
    protected function singleModel(int $remoteSiteId, int $remoteContentId): ModelInterface
    {
        $url = new SimpleUrl(get_permalink($remoteContentId));
        $language = new Bcp47Tag($this->siteSettingsRepository->siteLanguageTag($remoteSiteId));
        $label = siteLocaleName($remoteSiteId);

        return new Model($url, $language, $label);
    }

    /**
     * Create a NetworkState Instance
     *
     * Basically a wrapper for a static constructor that's difficult to mock in unit tests.
     *
     * @return NetworkState
     */
    protected function networkState(): NetworkState
    {
        return NetworkState::create();
    }
}
