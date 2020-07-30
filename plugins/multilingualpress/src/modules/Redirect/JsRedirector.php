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

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use function Inpsyde\MultilingualPress\currentSiteLocale;
use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Class JsRedirector
 * @package Inpsyde\MultilingualPress\Module\Redirect
 */
final class JsRedirector implements Redirector
{
    const FILTER_UPDATE_INTERVAL = 'multilingualpress.noredirect_update_interval';
    const SCRIPT_HANDLE = 'multilingualpress-redirect';

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var LanguageUrlDictionaryFactory
     */
    private $languageUrlDictionaryFactory;

    /**
     * @param LanguageUrlDictionaryFactory $languageUrlDictionaryFactory
     * @param AssetManager $assetManager
     */
    public function __construct(
        LanguageUrlDictionaryFactory $languageUrlDictionaryFactory,
        AssetManager $assetManager
    ) {

        $this->languageUrlDictionaryFactory = $languageUrlDictionaryFactory;
        $this->assetManager = $assetManager;
    }

    /**
     * @inheritdoc
     * @throws AssetException
     * @throws NonexistentTable
     */
    public function redirect()
    {
        $urls = $this->languageUrlDictionaryFactory->create();
        if (!$urls) {
            return;
        }

        /**
         * Filters the lifetime, in seconds, for data in the noredirect storage.
         *
         * @param int $lifetime
         */
        $lifetime = (int)apply_filters(
            NoRedirectStorage::FILTER_LIFETIME,
            NoRedirectStorage::LIFETIME_IN_SECONDS
        );

        /**
         * Filters the update interval, in seconds, for the timestamp of noredirect storage data.
         *
         * @param int $updateInterval
         */
        $updateInterval = (int)apply_filters(
            self::FILTER_UPDATE_INTERVAL,
            MINUTE_IN_SECONDS
        );

        try {
            $this->assetManager->enqueueScriptWithData(
                self::SCRIPT_HANDLE,
                'MultilingualPressRedirectorSettings',
                [
                    'currentLanguage' => currentSiteLocale(),
                    'noredirectKey' => NoredirectPermalinkFilter::QUERY_ARGUMENT,
                    'storageLifetime' => absint($lifetime * 1000),
                    'updateTimestampInterval' => absint($updateInterval * 1000),
                    'urls' => $urls,
                ],
                false
            );
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        } catch (NonexistentTable $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }
    }
}
