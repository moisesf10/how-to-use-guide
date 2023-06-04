<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceAuthorizationUser;
use App\Models\WorkspaceBlock;
use App\Models\WorkspaceEditor;
use App\Models\WorkspaceTopic;
use App\Models\WorkspaceUser;

class WorkspacePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function allowsAccessBlock($user, Workspace $workspace, WorkspaceBlock $block){
        if($workspace->indicates_public_access){
            return true;
        }

        if(empty($user)){
            return false;
        }

        $authorized = false;
        switch($user->getTable()){
            case 'workspace_users':

                $existsBlock = WorkspaceTopic::leftJoin('workspace_authorization_users as au', 'au.workspace_topic_id', '=', 'workspace_topics.id')
                    ->leftJoin('workspace_users as u', 'u.id', '=', 'au.workspace_user_id')
                    ->where('workspace_topics.workspace_block_id', $block->id)
                    ->where('u.id', auth()->user()->id)
                    ->where(function ($q){
                        $q->whereRaw("u.authorization_type = 'full'")
                            ->orWhereRaw("u.id is not null");
                    })
                    ->exists();



                if($existsBlock){
                    $authorized = true;
                }
                break;
            case 'users':
                $editor = WorkspaceEditor::where('workspace_id', $workspace->id)
                    ->where('user_id', $user->id)
                    ->whereRaw("status = 'accepted'")
                    ->exists();
                if($editor){
                    $authorized = true;
                }
                break;
        }

        return $authorized;

    }

    public function allowsAccessTopic($user, Workspace $workspace, WorkspaceTopic $topic){

        if($workspace->indicates_public_access){
            return true;
        }

        if(empty($user) ){
            return false;
        }

        $authorized = false;
        switch($user->getTable()){
            case 'workspace_users':
                $workspaceUser = WorkspaceUser::where('workspace_id', $workspace->id)
                    ->where('email', $user->email)
                    ->where('authorization_token', $user->authorization_token)
                    ->whereRaw("indicates_enabled = 1")
                    ->exists();

                if($workspaceUser){
                     if($user->authorization_type == 'full'){
                         $authorized = true;
                     }else{
                         if(! empty($topic)) {
                             if(WorkspaceAuthorizationUser::where('workspace_user_id', $user->id)
                                 ->where('workspace_topic_id', $topic->id)
                                 ->exists()){
                                 $authorized = true;
                             }
                         }
                     }

                }
                break;
            case 'users':
                $editor = WorkspaceEditor::where('workspace_id', $workspace->id)
                    ->where('user_id', $user->id)
                    ->whereRaw("status = 'accepted'")
                    ->exists();
                if($editor){
                    $authorized = true;
                }
                break;
        }

        return $authorized;

    }

    public function allowsAccessLandingPage($user, Workspace $workspace){

        if($workspace->indicates_public_access){
            return true;
        }

        if(empty($user)){
            return false;
        }

        $authorized = false;
        switch($user->getTable()){
            case 'workspace_users':
                $workspaceUser = WorkspaceUser::where('workspace_id', $workspace->id)
                    ->where('email', $user->email)
                    ->where('authorization_token', $user->authorization_token)
                    ->whereRaw("indicates_enabled = 1")
                    ->exists();

                if($workspaceUser){
                    $authorized = true;
                }
                break;
            case 'users':
                $editor = WorkspaceEditor::where('workspace_id', $workspace->id)
                    ->where('user_id', $user->id)
                    ->whereRaw("status = 'accepted'")
                    ->exists();
                if($editor){
                    $authorized = true;
                }
                break;
        }

        return $authorized;

    }
}
