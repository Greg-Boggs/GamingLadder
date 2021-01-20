<?php
if (!class_exists('WlAvatar')) {

    class WlAvatar
    {
        static protected $avatars = [
            'No avatar' => 'No avatar.gif',
            'D Blademaster' => 'D Blademaster.gif',
            'D Burner' => 'D Burner.gif',
            'D Clasher' => 'D Clasher.gif',
            'D Hurricane' => 'D Hurricane.gif',
            'D Sky Defend' => 'D Sky Defend.gif',
            'E High Lord' => 'elvish-high-lord.png',
            'E Lady' => 'elvish-lady.png',
            'E Marksman' => 'elvish-marksman-female.png',
            'G Impaler Arrack' => 'G Impaler Arrack.gif',
            'G Pillager' => 'goblin-pillager.png',
            'G Spearman' => 'G Spearman.gif',
            'G Wolf Rider' => 'G Wolf Rider.gif',
            'H Arch Mage Female' => 'H Arch Mage Female.gif',
            'H Footpad Female' => 'H Footpad Female.gif',
            'H Magi' => 'H Magi.gif',
            'H White Mage' => 'H White Mage.gif',
            'K Berserker' => 'dwarvish-berserker.png',
            'K Dragonguard' => 'K Dragonguard.gif',
            'K Flamethrower' => 'dwarvish-flamethrower.png',
            'K Footpad' => 'K Footpad.gif',
            'K Gryphon Rider' => 'K Gryphon Rider.gif',
            'K Guard' => 'K Guard.gif',
            'K Lord' => 'K Lord.gif',
            'K Pathfinder' => 'dwarvish-pathfinder.png',
            'K Thunderer' => 'dwarvish-thunderer.png',
            'K Warrior' => 'K Warrior.gif',
            'L Cavalry' => 'L Cavalry.gif',
            'L Duelist Die' => 'L Duelist Die.gif',
            'L Fencer' => 'L Fencer.gif',
            'L Javelineer' => 'L Javelineer.gif',
            'L Lieutenant' => 'human-lieutenant.png',
            'L Royal Guard' => 'L Royal Guard.gif',
            'L Shocktrooper' => 'L Shocktrooper.gif',
            'M Fighter Defend' => 'M Fighter Defend.gif',
            'N Fighter' => 'naga-fighter.png',
            'O Assassin' => 'orcish-assassin.png',
            'O Grunt' => 'orcish-grunt.png',
            'O Shaman' => 'orcish-shaman.png',
            'O Slayer' => 'orcish-slayer.png',
            'O Thief' => 'outlaws-thief.png',
            'O Young Ogre' => 'O Young Ogre.gif',
            'R Archer Female Melee' => 'R Archer Female Melee.gif',
            'R Hero Melee' => 'R Hero Melee.gif',
            'R Scout' => 'R Scout.gif',
            'R Shaman' => 'R Shaman.gif',
            'S Soothsayer Magic' => 'S Soothsayer Magic.gif',
            'T Whelp' => 'T Whelp.gif',
            'U Adept2' => 'U Adept2.gif',
            'U Adept' => 'U Adept.gif',
            'U Blood Bat' => 'U Blood Bat.gif',
            'U DeathBlade' => 'U DeathBlade.gif',
            'U Ghost' => 'U Ghost.gif',
            'U Ghost2' => 'undead-ghost.png',
            'U Necrophage' => 'U Necrophage.gif',
            'U Skeleton' => 'U Skeleton.gif',
            'U WCorpse' => 'U WCorpse.gif',
            'U Wose' => 'soulless-wose.png',
            'W Wose' => 'W Wose.gif',

            'U Sorcerer' => 'undead-sorcerer.png',
        ];

        public static function all()
        {
            // reserved U Sorcerer ;-)
            if (isset($_SESSION['username']) && $_SESSION['username'] != 'Maboul') {
                return array_diff_key(self::$avatars, ['U Sorcerer' => true]);
            }
            return self::$avatars;
        }

        public static function image($name)
        {
            $img = isset(self::$avatars[$name])
                ? self::$avatars[$name]
                : self::$avatars['No avatar'];
            return sprintf(
                '<img class="avatar %s" src="avatars/%s" alt="avatar" />',
                substr($img, -3),
                $img
            );
        }
    }
}
