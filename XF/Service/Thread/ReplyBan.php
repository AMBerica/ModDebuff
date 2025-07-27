<?php

namespace lulzapps\ModDebuff\XF\Service\Thread;

class ReplyBan extends XFCP_ReplyBan
{
    protected function _validate()
    {
        $errors = parent::_validate();

        if (isset($errors['is_staff'])) 
        {
            unset($errors['is_staff']);
        }

        // admins and super modrators cannot be reply banned
        if ($this->user->is_admin)
        {
            $errors['is_staff'] = 'Administrators cannot be reply banned.';
            return $errors;
        }

        $finder = \XF::finder('XF:Moderator');
        $moderator = $finder->where('user_id', $this->user->user_id)->fetchOne();
        if ($moderator && $moderator->is_super_moderator)
        {
            $errors['is_staff'] = 'Super moderators cannot be reply banned.';
            return $errors;
        }

        $finder = \XF::finder('XF:Node');
        $node = $finder->where('node_id', $this->thread->node_id)->fetchOne();
        if ($node->Moderators)
		{
			/** @var \XF\Entity\ModeratorContent $moderator */
			foreach ($node->Moderators AS $moderator)
			{
				if ($moderator->user_id == $this->user->user_id)
                {
                    $errors['is_staff'] = 'Forum moderators cannot be reply banned in their own forums.';
                    return $errors;
                }
			}
        }

        return $errors;
    }

}