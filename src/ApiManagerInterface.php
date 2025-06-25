<?php

namespace rdoepner\CleverReach;

interface ApiManagerInterface
{
    /**
     * Creates a subscriber.
     *
     * @param string $email
     * @param int    $groupId
     * @param bool   $active
     * @param array  $attributes
     * @param array  $globalAttributes
     * @param string $source
     * @param array  $tags
     *
     * @return mixed
     */
    public function createSubscriber(
        string $email,
        int $groupId,
        bool $active = false,
        array $attributes = [],
        array $globalAttributes = [],
        string $source = null,
        array $tags = []
    );

    /**
     * Update a subscriber.
     *
     * @param string $email
     * @param int    $groupId
     * @param array  $attributes
     * @param array  $globalAttributes
     * @param string $source
     * @param array  $tags
     *
     * @return mixed
     */
    public function updateSubscriber(
        string $email,
        int $groupId,
        array $attributes = [],
        array $globalAttributes = [],
        string $source = null,
        array $tags = []
    );

    /**
     * Returns a subscriber.
     *
     * @param string   $email
     * @param int|null $groupId
     *
     * @return mixed
     */
    public function getSubscriber(string $email, int $groupId = null);

    /**
     * @param int $groupId
     * @param ?int $filterId Give an id of the segment (ctfid in Frontend URI) if you want only that part of the group.
     * @return mixed
     */
    public function getSubscribers(int $groupId, ?int $filterId = null);

    /**
     * Sets the active status of a subscriber.
     *
     * @param string $email
     * @param int    $groupId
     * @param bool   $active
     *
     * @return mixed
     */
    public function setSubscriberStatus(string $email, int $groupId, $active = true);

    /**
     * Triggers the Double-Opt-In email for a subscriber.
     *
     * @param string $email
     * @param int    $formId
     * @param array  $options
     *
     * @return mixed
     */
    public function triggerDoubleOptInEmail(string $email, int $formId, array $options = []);

    /**
     * Triggers the Double-Opt-Out email for a subscriber.
     *
     * @param string $email
     * @param int    $formId
     * @param array  $options
     *
     * @return mixed
     */
    public function triggerDoubleOptOutEmail(string $email, int $formId, array $options = []);

    /**
     * Deletes a subscriber.
     *
     * @param string $email
     * @param int    $groupId
     *
     * @return mixed
     */
    public function deleteSubscriber(string $email, int $groupId);

    /**
     * Get attributes.
     *
     * @param int    $groupId
     *
     * @return mixed
     */
    public function getAttributes(int $groupId);

    /**
     * Creates a new global or group attribute
     * @param string $name name for internal use.
     * @param string $type type of data (text|number|gender|date)
     * @param int $groupId ID of group - leave empty for global attributes
     * @param string $description
     * @param string $preview_value
     * @param string $default_value
     * @return mixed
     */
    public function createAttribute(string $name, string $type, int $groupId = 0, string $description = "", string $previewValue ="",string $defaultValue="" );

    /**
     * @param int $id attribute id
     * @return mixed
     */
    public function deleteAttribute(int $id);
    /**
     * Update the attributes of a subscriber.
     *
     * @param int    $poolId
     * @param int    $attributeId
     * @param string $value
     *
     * @return mixed
     */
    public function updateSubscriberAttributes(int $poolId, int $attributeId, string $value);

    /**
     * Replace the tags of a subscriber.
     *
     * The new tags replace the existing ones! Use getSubscriber() to retrieve
     * existing tags before.
     *
     * @param string $email
     * @param int    $groupId
     * @param array  $tags
     *
     * @return mixed
     */
    public function replaceSubscriberTags(string $email, int $groupId, array $tags);

    /**
     * Retrieves an array of groups the given receiver is allocated to
     *
     * @param string $email
     *
     * @return mixed
     */
    public function getSubscriberGroups(string $email);
}
