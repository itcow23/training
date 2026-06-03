<?php

namespace app\commands;

use app\rbac\AuthorRule;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $authorRule = new AuthorRule();
        $auth->add($authorRule);

        //category permissions
        $categoryView = $auth->createPermission('category.view');
        $auth->add($categoryView);

        $categoryCreate = $auth->createPermission('category.create');
        $auth->add($categoryCreate);

        $categoryUpdate = $auth->createPermission('category.update');
        $auth->add($categoryUpdate);

        $categoryDelete = $auth->createPermission('category.delete');
        $auth->add($categoryDelete);

        //product permissions
        $productView = $auth->createPermission('product.view');
        $auth->add($productView);

        $productCreate = $auth->createPermission('product.create');
        $auth->add($productCreate);

        $productUpdate = $auth->createPermission('product.update');
        $auth->add($productUpdate);

        $productDelete = $auth->createPermission('product.delete');
        $auth->add($productDelete);

        //post_category and tag
        $postCategoryManage = $auth->createPermission('post_category.manage');
        $auth->add($postCategoryManage);

        $tagManage = $auth->createPermission('tag.manage');
        $auth->add($tagManage);

        //post permissions
        $postView = $auth->createPermission('post.view');
        $auth->add($postView);

        $postCreate = $auth->createPermission('post.create');
        $auth->add($postCreate);

        $postUpdate = $auth->createPermission('post.update');
        $auth->add($postUpdate);

        $postDelete = $auth->createPermission('post.delete');
        $auth->add($postDelete);

        $postUpdateOwn = $auth->createPermission('post.update_own');
        $postUpdateOwn->ruleName = $authorRule->name;
        $auth->add($postUpdateOwn);
        $auth->addChild($postUpdateOwn, $postUpdate);

        $postPublish = $auth->createPermission('post.publish');
        $auth->add($postPublish);

        //order permissions
        $orderView = $auth->createPermission('order.view');
        $auth->add($orderView);

        $orderUpdateStatus = $auth->createPermission('order.update_status');
        $auth->add($orderUpdateStatus);

        $orderCancel = $auth->createPermission('order.cancel');
        $auth->add($orderCancel);

        $orderDelete = $auth->createPermission('order.delete');
        $auth->add($orderDelete);

        //account and admin permissions
        $accountView = $auth->createPermission('account.view');
        $auth->add($accountView);

        $accountManage = $auth->createPermission('account.manage');
        $auth->add($accountManage);

        $rbacManage = $auth->createPermission('rbac.manage');
        $auth->add($rbacManage);

        //moderate comment and rating, coupon, media
        $commentModerate = $auth->createPermission('comment.moderate');
        $auth->add($commentModerate);

        $ratingModerate = $auth->createPermission('rating.moderate');
        $auth->add($ratingModerate);

        $couponManage = $auth->createPermission('coupon.manage');
        $auth->add($couponManage);

        $mediaDelete = $auth->createPermission('media.delete');
        $auth->add($mediaDelete);

        // comment
        $commentUpdate = $auth->createPermission('comment.update');
        $auth->add($commentUpdate);

        $commentDelete = $auth->createPermission('comment.delete');
        $auth->add($commentDelete);

        $commentUpdateOwn = $auth->createPermission('comment.update_own');
        $commentUpdateOwn->ruleName = $authorRule->name;
        $auth->add($commentUpdateOwn);
        $auth->addChild($commentUpdateOwn, $commentUpdate);

        $commentDeleteOwn = $auth->createPermission('comment.delete_own');
        $commentDeleteOwn->ruleName = $authorRule->name;
        $auth->add($commentDeleteOwn);
        $auth->addChild($commentDeleteOwn, $commentDelete);

        //roles customer
        $customerRole = $auth->createRole('customer');
        $auth->add($customerRole);
        $auth->addChild($customerRole, $commentUpdateOwn);
        $auth->addChild($customerRole, $commentDeleteOwn);

        //role editor
        $editorRole = $auth->createRole('editor');
        $auth->add($editorRole);
        $auth->addChild($editorRole, $postCreate);
        $editorRole->ruleName = $authorRule->name;
        $auth->addChild($editorRole, $postUpdateOwn);
        $auth->addChild($editorRole, $tagManage);

        //role sales
        $salesRole = $auth->createRole('sales');
        $auth->add($salesRole);
        $auth->addChild($salesRole, $orderView);
        $auth->addChild($salesRole, $orderUpdateStatus);
        $auth->addChild($salesRole, $orderCancel);

        //role manager
        $managerRole = $auth->createRole('manager');
        $auth->add($managerRole);

        //manager manage product and category
        $auth->addChild($managerRole, $categoryView);
        $auth->addChild($managerRole, $categoryCreate);
        $auth->addChild($managerRole, $categoryUpdate);

        $auth->addChild($managerRole, $productView);
        $auth->addChild($managerRole, $productCreate);
        $auth->addChild($managerRole, $productUpdate);

        //manager manage post
        $auth->addChild($managerRole, $postView);
        $auth->addChild($managerRole, $postCreate);
        $auth->addChild($managerRole, $postUpdate);
        $auth->addChild($managerRole, $postPublish);

        //manager check comment and rating
        $auth->addChild($managerRole, $commentModerate);
        $auth->addChild($managerRole, $commentUpdate);
        $auth->addChild($managerRole, $commentDelete);
        $auth->addChild($managerRole, $ratingModerate);
        $auth->addChild($managerRole, $couponManage);

        $auth->addChild($managerRole, $editorRole);
        $auth->addChild($managerRole, $salesRole);

        // role admin
        $adminRole = $auth->createRole('admin');
        $auth->add($adminRole);
        $auth->addChild($adminRole, $managerRole);

        // admin can delete everything
        $auth->addChild($adminRole, $categoryDelete);
        $auth->addChild($adminRole, $productDelete);
        $auth->addChild($adminRole, $postDelete);
        $auth->addChild($adminRole, $orderDelete);
        $auth->addChild($adminRole, $mediaDelete);
        $auth->addChild($adminRole, $accountView);

        $auth->addChild($adminRole, $accountManage);

        // role super admin
        $superAdminRole = $auth->createRole('super_admin');
        $auth->add($superAdminRole);

        $auth->addChild($superAdminRole, $rbacManage);
        $auth->addChild($superAdminRole, $accountManage);

        $auth->addChild($superAdminRole, $adminRole);

        $auth->assign($superAdminRole, 41);
        $auth->assign($managerRole, 42);
        $auth->assign($adminRole, 43);
        $auth->assign($editorRole, 44);
        $auth->assign($salesRole, 45);
        $auth->assign($customerRole, 46);

        echo "RBAC đã được khởi tạo thành công.\n";
    }
}
