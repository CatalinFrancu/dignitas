<?php

class Statement extends BaseObject {
  use DuplicateTrait, FlaggableTrait, MarkdownTrait, VotableTrait;

  function getObjectType() {
    return self::TYPE_STATEMENT;
  }

  function getMarkdownFields() {
    return [ 'context' ];
  }

  function getEntity() {
    return Entity::get_by_id($this->entityId);
  }

  function getUser() {
    return User::get_by_id($this->userId);
  }

  /**
   * Returns this Statement's answers, filtered by visibility to the current
   * user.
   */
  function getAnswers() {
    $answers = Model::factory('Answer')
      ->where('statementId', $this->id)
      ->where_not_equal('status', Ct::STATUS_PENDING_EDIT);

    if (!User::may(User::PRIV_DELETE_ANSWER)) {
      $answers = $answers
        ->where('status', Ct::STATUS_ACTIVE);
    }

    return $answers
      ->order_by_desc('createDate')
      ->find_many();
  }

  function getSources() {
    return Model::factory('StatementSource')
      ->where('statementId', $this->id)
      ->order_by_asc('rank')
      ->find_many();
  }

  function getTags() {
    return ObjectTag::getTags($this);
  }

  /**
   * Returns human-readable information about the status of this Statement.
   *
   * @return array a tuple of (human-readable status, human-readable sentence,
   * CSS class). If the status is active, returns null.
   */
  function getStatusInfo() {
    if (!in_array($this->status, [Ct::STATUS_CLOSED, Ct::STATUS_DELETED])) {
      return null;
    }

    $rec = [];
    $dup = $this->getDuplicate();

    $rec['status'] = $dup
      ? _('duplicate')
      : ($this->status == Ct::STATUS_CLOSED
         ? _('closed')
         : _('deleted'));

    $rec['dup'] = $dup;

    $rec['cssClass'] = ($this->status == Ct::STATUS_DELETED)
      ? 'alert-danger'
      : 'alert-warning';

    $rec['details'] = ($this->status == Ct::STATUS_CLOSED)
      ? _('This statement was closed')
      : _('This statement was deleted');

    switch ($this->reason) {
      case Ct::REASON_SPAM: $r = _('because it is spam.'); break;
      case Ct::REASON_ABUSE: $r = _('because it is rude or abusive.'); break;
      case Ct::REASON_DUPLICATE: $r = _('as a duplicate of the statement'); break;
      case Ct::REASON_OFF_TOPIC: $r = _('because it is off-topic.'); break;
      case Ct::REASON_UNVERIFIABLE: $r = _('because it is unverifiable.'); break;
      case Ct::REASON_LOW_QUALITY: $r = _('because it is low-quality.'); break;
      case Ct::REASON_BY_OWNER: $r = _('by the user who added it.'); break;
      case Ct::REASON_BY_USER: $r = _('by'); break;
      case Ct::REASON_OTHER: $r = _('for other reasons.'); break;
      default: $r = '';
    }
    $rec['details'] .= ' ' . $r;

    return $rec;
  }

  function isViewable() {
    return
      ($this->status != Ct::STATUS_PENDING_EDIT) &&
      (($this->status != Ct::STATUS_DELETED) ||
       User::may(User::PRIV_DELETE_STATEMENT));
  }

  function isEditable() {
    return
      ($this->status != Ct::STATUS_PENDING_EDIT) &&
      (User::may(User::PRIV_EDIT_STATEMENT) ||   // can edit any statements
       $this->userId == User::getActiveId());    // can always edit user's own statements
  }

  /**
   * Checks whether the statement's owner can delete it.
   * Current policy: deletable if
   *   - it has no answers; or
   *   - it has one answer with a zero or negative score.
   *
   * @return bool
   */
  function isDeletableByOwner() {
    $answers = Answer::get_all_by_statementId_status($this->id, Ct::STATUS_ACTIVE);

    switch (count($answers)) {
      case 0: return true;
      case 1: return ($answers[0]->score <= 0);
      default: return false;
    }
  }

  function isDeletable() {
    if (!$this->id) {
      return false; // not on the add statement page
    } else if (in_array($this->status, [Ct::STATUS_DELETED, Ct::STATUS_PENDING_EDIT])) {
      return false; // already deleted or pending edit
    } else if (User::may(User::PRIV_DELETE_STATEMENT)) {
      return true;  // can delete any statement
    } else if (($this->userId == User::getActiveId()) && $this->isDeletableByOwner()) {
      return true;  // owner can delete it
    } else {
      return false;
    }
  }

}
