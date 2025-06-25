<?php

namespace rdoepner\CleverReach;

use rdoepner\CleverReach\Http\AdapterInterface as HttpAdapter;

class ApiManager implements ApiManagerInterface
{
    /**
     * @var HttpAdapter
     */
    protected $adapter;

    /**
     * ApiManager constructor.
     *
     * @param HttpAdapter $adapter
     */
    public function __construct(HttpAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function createSubscriber(
        string $email,
        int $groupId,
        bool $active = false,
        array $attributes = [],
        array $globalAttributes = [],
        string $source = null,
        array $tags = []
    ) {
        $now = time();

        return $this->adapter->action(
            'post',
            "/v3/groups.json/{$groupId}/receivers",
            [
                'email' => $email,
                'registered' => $now,
                'activated' => $active ? $now : 0,
                'source' => $source,
                'attributes' => $attributes,
                'global_attributes' => $globalAttributes,
                'tags' => $tags
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateSubscriber(
        string $email,
        int $groupId,
        array $attributes = [],
        array $globalAttributes = [],
        string $source = null,
        array $tags = []
    ) {
        
        return $this->adapter->action(
            'put',
            "/v3/groups.json/{$groupId}/receivers/{$email}",
            [
                'source' => $source,
                'attributes' => $attributes,
                'global_attributes' => $globalAttributes,
                'tags' => $tags
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriber(string $email, int $groupId = null)
    {
        if ($groupId) {
            return $this->adapter->action('get', "/v3/groups.json/{$groupId}/receivers/{$email}");
        }

        return $this->adapter->action('get', "/v3/receivers.json/{$email}");
    }

    /**
     * @inheritDoc
     */
    public function getSubscribers(int $groupId, int $filterId = null){
        if (!is_null($filterId)) {
            return $this->adapter->action('get', "/v3/groups.json/{$groupId}/filters/{$filterId}/receivers?pagesize=5000");
        }else {
            return $this->adapter->action('get', "/v3/groups.json/{$groupId}/receivers?pagesize=5000");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscriberStatus(string $email, int $groupId, $active = true)
    {
        if ($active) {
            return $this->adapter->action('put', "/v3/groups.json/{$groupId}/receivers/{$email}/activate");
        }

        return $this->adapter->action('put', "/v3/groups.json/{$groupId}/receivers/{$email}/deactivate");
    }

    /**
     * {@inheritdoc}
     */
    public function triggerDoubleOptInEmail(string $email, int $formId, array $options = [])
    {
        return $this->adapter->action(
            'post',
            "/v3/forms.json/{$formId}/send/activate",
            [
                'email' => $email,
                'doidata' => array_merge(
                    [
                        'user_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                        'referer' => $_SERVER['HTTP_REFERER'] ?? 'http://localhost',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'FakeAgent/2.0 (Ubuntu/Linux)',
                    ],
                    $options
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function triggerDoubleOptOutEmail(string $email, int $formId, array $options = [])
    {
        return $this->adapter->action(
            'post',
            "/v3/forms.json/{$formId}/send/deactivate",
            [
                'email' => $email,
                'doidata' => array_merge(
                    [
                        'user_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                        'referer' => $_SERVER['HTTP_REFERER'] ?? 'http://localhost',
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'FakeAgent/2.0 (Ubuntu/Linux)',
                    ],
                    $options
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSubscriber(string $email, int $groupId)
    {
        return $this->adapter->action('delete', "/v3/groups.json/{$groupId}/receivers/{$email}");
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(int $groupId = 0)
    {
        return $this->adapter->action('get', "/v3/attributes.json?group_id={$groupId}");
    }

    /**
     * {@inheritdoc}
     */
    public function createAttribute(string $name, string $type, int $groupId = 0, string $description = "", string $previewValue = "", string $defaultValue = "")
    {
        return $this->adapter->action("post","/v3/attributes.json", [
            "name"=>$name,
            "type"=>$type,
            "group_id"=>$groupId,
            "description"=>$description,
            "preview_value"=>$previewValue,
            "default_value"=>$defaultValue
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function deleteAttribute(int $id)
    {
        return $this->adapter->action('delete', "/v3/attributes.json/{$id}");
    }
    /**
     * {@inheritdoc}
     */
    public function updateSubscriberAttributes(int $poolId, int $attributeId, string $value)
    {
        return $this->adapter->action(
            'put',
            "/v3/receivers.json/{$poolId}/attributes/{$attributeId}",
            [
                'value' => $value,
            ]
        );    
    }

    /**
     * {@inheritdoc}
     */
    public function replaceSubscriberTags(string $email, int $groupId, array $tags)
    {
        return $this->adapter->action(
            'put',
            "/v3/groups.json/{$groupId}/receivers/{$email}",
            [
                'tags' => $tags,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriberGroups(string $email)
    {
        return $this->adapter->action('get', "/v3/receivers.json/{$email}/groups");
    }


    /**
     * Returns the HTTP adapter.
     *
     * @return HttpAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
