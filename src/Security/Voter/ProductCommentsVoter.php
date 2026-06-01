<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Security\Voter;

use Contao\BackendUser;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Grants access to modulate comments of products.
 *
 * In Contao 5.6+ the "tl_comments" back end module checks the
 * "contao_comment.access" security attribute (see
 * ContaoCommentsPermissions::USER_CAN_ACCESS_COMMENT). None of the built-in
 * voters support the "tl_product" source, which prevents non-admin users from
 * moderating product comments. This voter fills that gap and checks whether the
 * user is allowed to manage the parent catalog (the "products" permission) of the
 * commented product.
 */
class ProductCommentsVoter implements VoterInterface, CacheableVoterInterface
{
    private const ACCESS_COMMENT_ATTRIBUTE = 'contao_comment.access';

    public function __construct(
        private readonly Connection $connection,
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    public function supportsAttribute(string $attribute): bool
    {
        return self::ACCESS_COMMENT_ATTRIBUTE === $attribute;
    }

    public function supportsType(string $subjectType): bool
    {
        return 'array' === $subjectType;
    }

    public function vote(TokenInterface $token, $subject, array $attributes, Vote|null $vote = null): int
    {
        if (
            !\is_array($subject)
            || !isset($subject['source'], $subject['parent'])
            || 'tl_product' !== $subject['source']
            || !array_filter($attributes, $this->supportsAttribute(...))
        ) {
            return self::ACCESS_ABSTAIN;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return self::ACCESS_GRANTED;
        }

        return $this->hasAccess($token, (int) $subject['parent'])
            ? self::ACCESS_GRANTED
            : self::ACCESS_DENIED;
    }

    private function hasAccess(TokenInterface $token, int $parent): bool
    {
        $user = $token->getUser();

        if (!$user instanceof BackendUser) {
            return false;
        }

        $catalogId = $this->connection->fetchOne('SELECT pid FROM tl_product WHERE id = ?', [$parent]);

        if (false === $catalogId) {
            return false;
        }

        $products = $user->products;

        if (!\is_array($products) || [] === $products) {
            return false;
        }

        return \in_array((int) $catalogId, array_map(intval(...), $products), true);
    }
}
