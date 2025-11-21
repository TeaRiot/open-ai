<?php

namespace TeaRiot\OpenAi;

class Url
{
    public const ORIGIN = 'https://api.openai.com';
    public const API_VERSION = 'v1';
    public const OPEN_AI_URL = self::ORIGIN . "/" . self::API_VERSION;
    public static $BASE_URL = '';

    public static function setBaseUrl(string $url): void
    {
        self::$BASE_URL = rtrim($url, '/');
    }

    protected static function base(string $path): string
    {
        if (self::$BASE_URL !== '') {
            return self::$BASE_URL . $path;
        }

        return self::OPEN_AI_URL . $path;
    }

    /**
     * @deprecated
     * @param string $engine
     * @return string
     */
    public static function completionURL(string $engine): string
    {
        return self::base("/engines/$engine/completions");
    }

    /**
     * @return string
     */
    public static function completionsURL(): string
    {
        return self::base("/completions");
    }

    /**
     *
     * @return string
     */
    public static function editsUrl(): string
    {
        return self::base("/edits");
    }

    /**
     * @param string $engine
     * @return string
     */
    public static function searchURL(string $engine): string
    {
        return self::base("/engines/$engine/search");
    }

    /**
     * @param
     * @return string
     */
    public static function enginesUrl(): string
    {
        return self::base("/engines");
    }

    /**
     * @param string $engine
     * @return string
     */
    public static function engineUrl(string $engine): string
    {
        return self::base("/engines/$engine");
    }

    /**
     * @param
     * @return string
     */
    public static function classificationsUrl(): string
    {
        return self::base("/classifications");
    }

    /**
     * @param
     * @return string
     */
    public static function moderationUrl(): string
    {
        return self::base("/moderations");
    }

    /**
     * @param
     * @return string
     */
    public static function transcriptionsUrl(): string
    {
        return self::base("/audio/transcriptions");
    }

    /**
     * @param
     * @return string
     */
    public static function translationsUrl(): string
    {
        return self::base("/audio/translations");
    }

    /**
     * @param
     * @return string
     */
    public static function filesUrl(): string
    {
        return self::base("/files");
    }

    /**
     * @param
     * @return string
     */
    public static function fineTuneUrl(): string
    {
        return self::base("/fine_tuning/jobs");
    }

    /**
     * @param
     * @return string
     */
    public static function fineTuneModel(): string
    {
        return self::base("/models");
    }

    /**
     * @param
     * @return string
     */
    public static function answersUrl(): string
    {
        return self::base("/answers");
    }

    /**
     * @param
     * @return string
     */
    public static function imageUrl(): string
    {
        return self::base("/images");
    }

    /**
     * @param
     * @return string
     */
    public static function embeddings(): string
    {
        return self::base("/embeddings");
    }

    /**
     * @param
     * @return string
     */
    public static function chatUrl(): string
    {
        return self::base("/chat/completions");
    }

    /**
     * @param
     * @return string
     */
    public static function assistantsUrl(): string
    {
        return self::base("/assistants");
    }

    /**
     * @param
     * @return string
     */
    public static function threadsUrl(): string
    {
        return self::base("/threads");
    }

    /**
     * @param
     * @return string
     */
    public static function ttsUrl(): string
    {
        return self::base("/audio/speech");
    }

    /**
     * Responses API root
     */
    public static function responsesUrl(): string
    {
        return self::base("/responses");
    }

    /**
     * Responses API: specific response
     */
    public static function responseUrl(string $responseId): string
    {
        return static::responsesUrl() . '/' . $responseId;
    }

    /**
     * Responses API: cancel a response
     */
    public static function responseCancelUrl(string $responseId): string
    {
        return static::responseUrl($responseId) . '/cancel';
    }

    /**
     * Responses API: input tokens (append / stream input)
     */
    public static function responseInputTokensUrl(string $responseId): string
    {
        return static::responseUrl($responseId) . '/input-tokens';
    }

    /**
     * Responses API: input items (structured input updates)
     */
    public static function responseInputItemsUrl(string $responseId): string
    {
        return static::responseUrl($responseId) . '/input-items';
    }
}
