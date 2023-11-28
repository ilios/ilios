<?php

declare(strict_types=1);

namespace App\Classes;

interface VoterPermissions
{
    public const VIEW = 'view';

    public const EDIT = 'edit';

    public const DELETE = 'delete';

    public const CREATE = 'create';

    public const UNLOCK = 'unlock';

    public const LOCK = 'lock';

    public const ARCHIVE = 'archive';

    public const ROLLOVER = 'rollover';

    public const CREATE_TEMPORARY_FILE = 'create_temporary_file';

    public const VIEW_VIRTUAL_LINK = 'view_virtual_link';

    public const VIEW_DRAFT_CONTENTS = 'view_draft_contents';
}
