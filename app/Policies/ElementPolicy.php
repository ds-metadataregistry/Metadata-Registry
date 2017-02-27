<?php

namespace App\Policies;

use App\Models\Access\User\User;
use App\Models\Element;
use App\Models\ElementSet;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElementPolicy
{
  use HandlesAuthorization;

  public function before(User $user)
  {
    if ($user->is_administrator) {
      return true;
    }
  }

  /**
   * Determine whether the user can view the element.
   *
   * @param  \App\Models\Access\User\User $user
   * @param  \App\Models\Element $element
   *
   * @return mixed
   */
  public function view(User $user, Element $element)
  {
    //
  }

  /**
   * Determine whether the user can create elements.
   *
   * @param  \App\Models\Access\User\User $user
   *
   * @return mixed
   */
  public function create(User $user, ElementSet $elementSet)
  {
    //User must be one of: admin, projectadmin, elementSetadmin
    if ($user->isMaintainerForElementSet($elementSet)) {
      return true;
    }
  }

  /**
   * Determine whether the user can update the element.
   *
   * @param  \App\Models\Access\User\User $user
   * @param  \App\Models\Element $element
   *
   * @return mixed
   */
  public function update(User $user, Element $element)
  {
    //User must be one of: admin, projectadmin, elementSetadmin
    if ($user->isMaintainerForElementSet($element->elementSet)) {
      return true;
    }
  }

  /**
   * Determine whether the user can delete the element.
   *
   * @param  \App\Models\Access\User\User $user
   * @param  \App\Models\Element $element
   *
   * @return mixed
   */
  public function delete(User $user, Element $element)
  {
    //User must be one of: admin, projectadmin, elementSetadmin
    if ($user->isMaintainerForElementSet($element->elementSet)) {
      return true;
    }
  }
}