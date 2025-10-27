<?php

namespace TeaRiot\OpenAi;

use Exception;

class OpenAi
{
    protected $engine = "davinci";
    protected $model = "text-davinci-002";
    protected $chatModel = "gpt-3.5-turbo";
    protected $assistantsBetaVersion = "v1";
    protected $headers;
    protected $contentTypes;
    protected $timeout = 0;
    protected $stream_method;
    protected $customUrl = "";

    protected $proxyIp = "";
    protected $proxyAccess = "";

    protected $curlInfo = [];

    protected $urlClass = Url::class;

    public function setProxyAccess(string $access)
    {
        $this->proxyAccess = $access;
    }

    public function setProxyIp(string $ip)
    {
        $this->proxyIp = $ip;
    }

    public function __construct($OPENAI_API_KEY)
    {
        $this->contentTypes = [
            "application/json" => "Content-Type: application/json",
            "multipart/form-data" => "Content-Type: multipart/form-data",
        ];

        $this->headers = [
            $this->contentTypes["application/json"],
            "Authorization: Bearer $OPENAI_API_KEY",
        ];
    }

    /**
     * @return array
     * Remove this method from your code before deploying
     */
    public function getCURLInfo(): array
    {
        return $this->curlInfo;
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    public function listModels()
    {
        $url = ($this->urlClass)::fineTuneModel();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $model
     * @return bool|string
     * @throws Exception
     */
    public function retrieveModel($model)
    {
        $model = "/$model";
        $url = ($this->urlClass)::fineTuneModel().$model;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     * @deprecated
     */
    public function complete($opts)
    {
        $engine = $opts['engine'] ?? $this->engine;
        $url = ($this->urlClass)::completionURL($engine);
        unset($opts['engine']);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param        $opts
     * @param  null  $stream
     * @return bool|string
     * @throws Exception
     */
    public function completion($opts, $stream = null)
    {
        if (array_key_exists('stream', $opts) && $opts['stream']) {
            if ($stream == null) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }

        $opts['model'] = $opts['model'] ?? $this->model;
        $url = ($this->urlClass)::completionsURL();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function createEdit($opts)
    {
        $url = ($this->urlClass)::editsUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function image($opts)
    {
        $url = ($this->urlClass)::imageUrl()."/generations";
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function imageEdit($opts)
    {
        $url = ($this->urlClass)::imageUrl()."/edits";
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function createImageVariation($opts)
    {
        $url = ($this->urlClass)::imageUrl()."/variations";
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     * @deprecated
     */
    public function search($opts)
    {
        $engine = $opts['engine'] ?? $this->engine;
        $url = ($this->urlClass)::searchURL($engine);
        unset($opts['engine']);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     * @deprecated
     */
    public function answer($opts)
    {
        $url = ($this->urlClass)::answersUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     * @deprecated
     */
    public function classification($opts)
    {
        $url = ($this->urlClass)::classificationsUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function moderation($opts)
    {
        $url = ($this->urlClass)::moderationUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param        $opts
     * @param  null  $stream
     * @return bool|string
     * @throws Exception
     */
    public function chat($opts, $stream = null)
    {
        if ($stream != null && array_key_exists('stream', $opts)) {
            if (! $opts['stream']) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }

        $opts['model'] = $opts['model'] ?? $this->chatModel;
        $url = ($this->urlClass)::chatUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function transcribe($opts)
    {
        $url = ($this->urlClass)::transcriptionsUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function translate($opts)
    {
        $url = ($this->urlClass)::translationsUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function uploadFile($opts)
    {
        $url = ($this->urlClass)::filesUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    public function listFiles()
    {
        $url = ($this->urlClass)::filesUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $file_id
     * @return bool|string
     * @throws Exception
     */
    public function retrieveFile($file_id)
    {
        $file_id = "/$file_id";
        $url = ($this->urlClass)::filesUrl().$file_id;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $file_id
     * @return bool|string
     * @throws Exception
     */
    public function retrieveFileContent($file_id)
    {
        $file_id = "/$file_id/content";
        $url = ($this->urlClass)::filesUrl().$file_id;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $file_id
     * @return bool|string
     * @throws Exception
     */
    public function deleteFile($file_id)
    {
        $file_id = "/$file_id";
        $url = ($this->urlClass)::filesUrl().$file_id;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'DELETE');
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function createFineTune($opts)
    {
        $url = ($this->urlClass)::fineTuneUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    public function listFineTunes()
    {
        $url = ($this->urlClass)::fineTuneUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $fine_tune_id
     * @return bool|string
     * @throws Exception
     */
    public function retrieveFineTune($fine_tune_id)
    {
        $fine_tune_id = "/$fine_tune_id";
        $url = ($this->urlClass)::fineTuneUrl().$fine_tune_id;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $fine_tune_id
     * @return bool|string
     * @throws Exception
     */
    public function cancelFineTune($fine_tune_id)
    {
        $fine_tune_id = "/$fine_tune_id/cancel";
        $url = ($this->urlClass)::fineTuneUrl().$fine_tune_id;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST');
    }

    /**
     * @param $fine_tune_id
     * @return bool|string
     * @throws Exception
     */
    public function listFineTuneEvents($fine_tune_id)
    {
        $fine_tune_id = "/$fine_tune_id/events";
        $url = ($this->urlClass)::fineTuneUrl().$fine_tune_id;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $fine_tune_id
     * @return bool|string
     * @throws Exception
     */
    public function deleteFineTune($fine_tune_id)
    {
        $fine_tune_id = "/$fine_tune_id";
        $url = ($this->urlClass)::fineTuneModel().$fine_tune_id;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'DELETE');
    }

    /**
     * @param
     * @return bool|string
     * @throws Exception
     * @deprecated
     */
    public function engines()
    {
        $url = ($this->urlClass)::enginesUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $engine
     * @return bool|string
     * @throws Exception
     * @deprecated
     */
    public function engine($engine)
    {
        $url = ($this->urlClass)::engineUrl($engine);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function embeddings($opts)
    {
        $url = ($this->urlClass)::embeddings();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function createAssistant($data)
    {
        $data['model'] = $data['model'] ?? $this->chatModel;
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $assistantId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveAssistant($assistantId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl() . '/' . $assistantId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $assistantId
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function modifyAssistant($assistantId, $data)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl() . '/' . $assistantId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $assistantId
     * @return bool|string
     * @throws Exception
     */
    public function deleteAssistant($assistantId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl() . '/' . $assistantId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'DELETE');
    }

    /**
     * @param array $query
     * @return bool|string
     * @throws Exception
     */
    public function listAssistants($query = [])
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl();
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $assistantId
     * @param string $fileId
     * @return bool|string
     * @throws Exception
     */
    public function createAssistantFile($assistantId, $fileId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl() . '/' . $assistantId . '/files';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', ['file_id' => $fileId]);
    }

    /**
     * @param string $assistantId
     * @param string $fileId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveAssistantFile($assistantId, $fileId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl() . '/' . $assistantId . '/files/' . $fileId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $assistantId
     * @param array $query
     * @return bool|string
     * @throws Exception
     */
    public function listAssistantFiles($assistantId, $query = [])
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl() . '/' . $assistantId . '/files';
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $assistantId
     * @param string $fileId
     * @return bool|string
     * @throws Exception
     */
    public function deleteAssistantFile($assistantId, $fileId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::assistantsUrl() . '/' . $assistantId . '/files/' . $fileId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'DELETE');
    }

    /**
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function createThread($data = [])
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $threadId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveThread($threadId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function modifyThread($threadId, $data)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $threadId
     * @return bool|string
     * @throws Exception
     */
    public function deleteThread($threadId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'DELETE');
    }

    /**
     * @param string $threadId
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function createThreadMessage($threadId, $data)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/messages';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $threadId
     * @param string $messageId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveThreadMessage($threadId, $messageId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/messages/' . $messageId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param string $messageId
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function modifyThreadMessage($threadId, $messageId, $data)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/messages/' . $messageId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $threadId
     * @param array $query
     * @return bool|string
     * @throws Exception
     */
    public function listThreadMessages($threadId, $query = [])
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/messages';
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param string $messageId
     * @param string $fileId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveMessageFile($threadId, $messageId, $fileId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/messages/' . $messageId . '/files/' . $fileId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param string $messageId
     * @param array $query
     * @return bool|string
     * @throws Exception
     */
    public function listMessageFiles($threadId, $messageId, $query = [])
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/messages/' . $messageId . '/files';
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function createRun($threadId, $data, $stream = null)
    {
        if (array_key_exists('stream', $data) && $data['stream']) {
            if ($stream == null) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }
        
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $threadId
     * @param string $runId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveRun($threadId, $runId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs/' . $runId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param string $runId
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function modifyRun($threadId, $runId, $data)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs/' . $runId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $threadId
     * @param array $query
     * @return bool|string
     * @throws Exception
     */
    public function listRuns($threadId, $query = [])
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs';
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param string $runId
     * @param array $outputs
     * @return bool|string
     * @throws Exception
     */
    public function submitToolOutputs($threadId, $runId, $outputs, $stream = null)
    {
        if (array_key_exists('stream', $outputs) && $outputs['stream']) {
            if ($stream == null) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }
        
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs/' . $runId . '/submit_tool_outputs';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $outputs);
    }

    /**
     * @param string $threadId
     * @param string $runId
     * @return bool|string
     * @throws Exception
     */
    public function cancelRun($threadId, $runId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs/' . $runId . '/cancel';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST');
    }

    /**
     * @param array $data
     * @return bool|string
     * @throws Exception
     */
    public function createThreadAndRun($data)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/runs';
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @param string $threadId
     * @param string $runId
     * @param string $stepId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveRunStep($threadId, $runId, $stepId)
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs/' . $runId . '/steps/' . $stepId;
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $threadId
     * @param string $runId
     * @param array $query
     * @return bool|string
     * @throws Exception
     */
    public function listRunSteps($threadId, $runId, $query = [])
    {
        $this->addAssistantsBetaHeader();
        $url = ($this->urlClass)::threadsUrl() . '/' . $threadId . '/runs/' . $runId . '/steps';
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param $opts
     * @return bool|string
     * @throws Exception
     */
    public function tts($opts)
    {
        $url = ($this->urlClass)::ttsUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param array $opts
     * @param null|callable $stream
     * @return bool|string
     * @throws Exception
     */
    public function response($opts, $stream = null)
    {
        if ($stream != null && array_key_exists('stream', $opts)) {
            if (! $opts['stream']) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }

        $opts['model'] = $opts['model'] ?? $this->chatModel;
        $url = ($this->urlClass)::responsesUrl();
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param string $responseId
     * @return bool|string
     * @throws Exception
     */
    public function retrieveResponse($responseId)
    {
        $url = ($this->urlClass)::responseUrl($responseId);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param array $query
     * @return bool|string
     * @throws Exception
     */
    public function listResponses($query = [])
    {
        $url = ($this->urlClass)::responsesUrl();
        if (count($query) > 0) {
            $url .= '?' . http_build_query($query);
        }
        $this->baseUrl($url);

        return $this->sendRequest($url, 'GET');
    }

    /**
     * @param string $responseId
     * @return bool|string
     * @throws Exception
     */
    public function deleteResponse($responseId)
    {
        $url = ($this->urlClass)::responseUrl($responseId);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'DELETE');
    }

    /**
     * @param string $responseId
     * @return bool|string
     * @throws Exception
     */
    public function cancelResponse($responseId)
    {
        $url = ($this->urlClass)::responseCancelUrl($responseId);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST');
    }

    /**
     * @param string $responseId
     * @param array $opts
     * @param null|callable $stream
     * @return bool|string
     * @throws Exception
     */
    public function responseInputTokens($responseId, $opts, $stream = null)
    {
        if ($stream != null && array_key_exists('stream', $opts)) {
            if (! $opts['stream']) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }

        $url = ($this->urlClass)::responseInputTokensUrl($responseId);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param string $responseId
     * @param array $opts
     * @param null|callable $stream
     * @return bool|string
     * @throws Exception
     */
    public function responseInputItems($responseId, $opts, $stream = null)
    {
        if ($stream != null && array_key_exists('stream', $opts)) {
            if (! $opts['stream']) {
                throw new Exception(
                    'Please provide a stream function. Check https://github.com/orhanerday/open-ai#stream-example for an example.'
                );
            }

            $this->stream_method = $stream;
        }

        $url = ($this->urlClass)::responseInputItemsUrl($responseId);
        $this->baseUrl($url);

        return $this->sendRequest($url, 'POST', $opts);
    }

    /**
     * @param  int  $timeout
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param  string  $customUrl
     * @deprecated
     */

    /**
     * @param  string  $customUrl
     * @return void
     */
    public function setCustomURL(string $customUrl)
    {
        if ($customUrl != "") {
            $this->customUrl = $customUrl;
        }
    }

    /**
     * @param  string  $customUrl
     * @return void
     */
    public function setBaseURL(string $customUrl)
    {
        if ($customUrl != '') {
            $this->customUrl = $customUrl;
        }
    }

    /**
     * @param  array  $header
     * @return void
     */
    public function setHeader(array $header)
    {
        if ($header) {
            foreach ($header as $key => $value) {
                $this->headers[$key] = $value;
            }
        }
    }

    /**
     * @param  string  $org
     */
    public function setORG(string $org)
    {
        if ($org != "") {
            $this->headers[] = "OpenAI-Organization: $org";
        }
    }

    /**
     * @param string $version
     */
    public function setAssistantsBetaVersion(string $version)
    {
        if ($version != "") {
            $this->assistantsBetaVersion = $version;
        }
    }

    /**
     * @return void
     */
    protected function addAssistantsBetaHeader(){
        $this->headers[] = 'OpenAI-Beta: assistants='.$this->assistantsBetaVersion;
    }


    /**
     * @param string $url
     * @param string $method
     * @param array $opts
     * @return bool|string
     * @throws Exception
     */
    protected function sendRequest(string $url, string $method, array $opts = [])
    {
        $post_fields = json_encode($opts);

        if (array_key_exists('file', $opts) || array_key_exists('image', $opts)) {
            $this->headers[0] = $this->contentTypes["multipart/form-data"];
            $post_fields = $opts;
        } else {
            $this->headers[0] = $this->contentTypes["application/json"];
        }
        $curl_info = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $post_fields,
            CURLOPT_HTTPHEADER     => $this->headers,
        ];

        if ($opts == []) {
            unset($curl_info[CURLOPT_POSTFIELDS]);
        }

        if (!empty($this->proxyAccess) && !empty($this->proxyIp)) {
            $curl_info[CURLOPT_PROXYUSERPWD] = $this->proxyAccess;
            $curl_info[CURLOPT_PROXY] = $this->proxyIp;
        }

        if (array_key_exists('stream', $opts) && $opts['stream']) {
            $curl_info[CURLOPT_WRITEFUNCTION] = $this->stream_method;
        }

        $curl = curl_init();

        try {
            curl_setopt_array($curl, $curl_info);
            $response = curl_exec($curl);

            $info = curl_getinfo($curl);
            $this->curlInfo = $info;

            if (curl_errno($curl)) {
                throw new \Exception(curl_error($curl));
            }

            return $response;
        } finally {
            curl_close($curl);
        }
    }

    /**
     * @param  string  $url
     */
    protected function baseUrl(string &$url)
    {
        if ($this->customUrl != "") {
            $url = str_replace(($this->urlClass)::ORIGIN, $this->customUrl, $url);
        }
    }
}
