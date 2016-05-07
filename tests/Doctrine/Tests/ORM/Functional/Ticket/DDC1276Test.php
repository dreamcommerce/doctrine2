<?php

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Tests\Models\CMS\CmsGroup;
use Doctrine\Tests\Models\CMS\CmsUser;

/**
 * @group DDC-1276
 */
class DDC1276Test extends \Doctrine\Tests\OrmFunctionalTestCase
{
    public function setUp()
    {
        $this->useModelSet('cms');
        parent::setUp();
    }

    public function testIssue()
    {
        $user = new CmsUser();
        $user->name = "Benjamin";
        $user->username = "beberlei";
        $user->status = "active";
        $this->_em->persist($user);

        for ($i = 0; $i < 2; $i++) {
            $group = new CmsGroup();
            $group->name = "group".$i;
            $user->groups[] = $group;
            $this->_em->persist($group);
        }
        $this->_em->flush();
        $this->_em->clear();

        $user = $this->_em->find(CmsUser::class, $user->id);
        $cloned = clone $user;

        self::assertSame($user->groups, $cloned->groups);
        self::assertEquals(2, count($user->groups));
        $this->_em->merge($cloned);

        self::assertEquals(2, count($user->groups));

        $this->_em->flush();
    }
}
