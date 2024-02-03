<?php

namespace App\Traits\UserAlert;

trait UserAlertConstantsTrait
{
    public const ALERT_TYPE_ERROR       = 'error';
    public const ALERT_TYPE_WARNING     = 'warning';
    public const ALERT_TYPE_FEEDFORWARD = 'feedforward';
    public const ALERT_TYPE_SUCCESS     = 'success';

    public const ALERT_VISUAL_REPRESENTATION_MESSAGE_BOX = 'message-box';
    public const ALERT_VISUAL_REPRESENTATION_SIMPLE_WORD = 'simple-word';

    public const ALERT_COLOR_RED    = 'red';
    public const ALERT_COLOR_YELLOW = 'yellow';
    public const ALERT_COLOR_BLUE   = 'blue';
    public const ALERT_COLOR_GREEN  = 'green';
    public const ALERT_COLOR_ORANGE = 'orange';

    public const ALERT_ICON_EXCLAMATION_TRIANGLE = 'fa-exclamation-triangle';
    public const ALERT_ICON_CHECK_CIRCLE         = 'fa-check-circle';
    public const ALERT_ICON_SAND_CLOCK           = 'fa-hourglass-half';

    public const ALERT_MOMENT_TRANSITION_OUT = 'transition-out';
}