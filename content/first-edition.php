<?php
$title_tag = 'First Edition Cards NFT | TheSpaceWar.com';
require(ROOT.'view/head.php');


if ( isset( $_POST['code'] ) && isset( $logged_in['id'] ) ) {
    if ( apcu_exists( 'wrongNFTcode_user:'.$logged_in['id'] ) || apcu_exists( 'wrongNFTcode_ip:'.$_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        echo '<div class="error">Try again in 30 seconds. Code has not been checked.</div>';
        apcu_store( 'wrongNFTcode_user:'.$logged_in['id'], '', 30 );
        apcu_store( 'wrongNFTcode_ip:'.$_SERVER['HTTP_CF_CONNECTING_IP'], '', 30 );
    } else {
        $verify = verifyNFTCode( $_POST['code'], $logged_in['id'] );
        if ( $verify === 'incorrect_code' ) {
            echo '<div class="error">The code is incorrect. Try again in 30 seconds.</div>';
            apcu_store( 'wrongNFTcode_user:'.$logged_in['id'], '', 30 );
            apcu_store( 'wrongNFTcode_ip:'.$_SERVER['HTTP_CF_CONNECTING_IP'], '', 30 );
        } elseif ( $verify === 'alread_owner' ) {
            echo '<div class="good">You are already the registered owner of this NFT. All is good.</div>';
        } elseif ( $verify === 'opensea_api_error' ) {
            echo '<div class="error">The NFT code is correct but I could not check the owner using the OpenSea API. Try to add a username first in your OpenSea account and then wait 30 minutes. If that does not work contact us for a solution.</div>';
        } elseif ( $verify === 'owner_not_changed' ) {
            echo '<div class="error">The NFT code is correct but the owner of this NFT did not change on OpenSea.</div>';
        } elseif ( $verify === 'all_ok' ) {
            echo '<div class="good">The NFT code is correct and you are now the registered owner!</div>';
        } else {
            echo '<div class="error">Unknown error, contact support.</div>';
        }
        if ( $verify === 'alread_owner' || $verify === 'all_ok' ) {
            echo '<div class="good">Link to high resolution image:<br>';
            $simple_hash = substr( md5( $_POST['code'] ), -15 );
            $parts = explode( ':', $_POST['code'] );
            $url = 'https://images.thespacewar.com/first-edition-high-res/'.$parts[0].'-'.$simple_hash.'.png';
            echo '<a href="'.$url.'" target="_blank">'.$url.'</a>';
            echo '<br>For private non-commercial use only (reselling this NFT for profit is ok).</div>';
        }
    }
}

$nft_first_edition = getNFTFirstEdition();

?>

<h1 style="text-transform: none;">First Edition Cards as NFTs</h1>

<p>Here you can buy the very first copy of a unique card in The Space War card game. With an NFT your ownership is cryptographically verified and certified forever on the decentralized Ethereum blockchain.</p>

<p>Total Supply: 102 unique cards, 1 NFT for each card.</p>

<p>The Space War has been in active development and playtesting phase since the summer 2018, it will be published and fully released later. Tournaments will be held both online and offline.</p>

<h2>Register Your NFT</h2>

<p>After you become the owner of a NFT you will receive an NFT Code.<br>

<?php if ($logged_in == []) { ?>
    First you need to <a href='/register'>register an account</a> or login in order to register your NFT:</p>
    <form method="post" action="">
        <input type="text" name="code" required minlength="40" maxlength="80" style="font-size:20px;">
        <input type="submit" name="register" style="font-size:20px;height: 44px;" value="Register NFT" disabled>
    </form>
<?php } else { ?>
    Make sure that you have your username set <a href="https://opensea.io/account/settings" target="_blank">here</a> and then input the NFT Code to register you (<strong><?=$logged_in['username']?></strong>) as the owner of the NFT:</p>
    <form method="post" action="">
        <input type="text" name="code" required minlength="40" maxlength="80" style="font-size:20px;">
        <input type="submit" name="register" style="font-size:20px;height: 44px;" value="Register NFT">
    </form>
<?php } ?>

<p>The code unlocks the following:</p>
<ul>
    <li>First physical copy of the card signed by Jim Westergren as #1 (once the game is physically published). See more info below.</li>
    <li>Your username displayed as owner of the NFT on the card info page with a link for people to place offers on OpenSea for your NFT as well as an image of the card on your user page.</li>
    <li>1 000 Credits for the game online.</li>
    <li>Secret URL to high resolution card image (652x910 png).</li>
</ul>

<h2>Listings</h2>

<?php

foreach ( $nft_first_edition as $card_slug => $array ) {
    echo '<div class="nftcards">';
    echo 'Price: ';
    if ( $array['token_id'] == '' ) {
        echo 'To be listed';
    } elseif ( $array['owner_opensea'] != '' ) {
        echo '<strong>SOLD</strong>';
    } else {
        echo $array['price'].' ETH';
    }
    echo '<br>';
    if ( $array['token_id'] != '' ) {
        echo '<a href="https://opensea.io/assets/0x495f947276749ce646f68ac8c248420045cb7b5e/'.$array['token_id'].'" target="_blank">Buy this NFT</a>';
    } else {
        echo 'Buy this NFT';
    }
    if ( substr( $card_slug, 0, 10 ) === 'commander-' ) {
        echo ' | <a href="/commanders/'.substr( $card_slug, 10 ).'">Card info</a><br>';
    } else {
        echo ' | <a href="/cards/'.$card_slug.'">Card info</a><br>';
    }

    echo '<img loading=lazy src="';
    if ( substr( $card_slug, 0, 10 ) === 'commander-' ) {
        echo 'https://images.thespacewar.com/commander-'.$array['img_id'].'.png">';
    } else {
        echo 'https://images.thespacewar.com/card-'.$array['img_id'].'.jpg">';
    }
    echo '</div>';
}
?>

<h2>Physical Cards signed by Jim Westergren</h2>

<p>When the first edition of the game has been physically printed (1-3 years from now) the creator is going to sign one of each card with "#1 Jim Westergren". No other copies will be signed with #1, only 1 copy of each card. We will then announce a date and time 2 weeks in advance and at that time we are going to email all the users on this site that are currently registered as owners of one or more NFTs and ask for a postal address to send the card(s) to. Make sure that have registered your NFT above and that your email is working.</p>

<h2>1 000 Credits</h2>

<p>When you register your NFT you will receive 1000 game credits. At least for until year 2022 (or later) this will be the only way to purchase credits in the game. Players will need to purchase a NFT on the secondary market (click on the same "Buy this NFT" link) if all listings has been sold.</p>

<h2>Prestige</h2>

<p>When you register your NFT the card page that players of the game study will display you as the owner of the first edition card with a link to the NFT page in OpenSea for people to place offers.</p>

<h2>Flipping</h2>

<p>It is of course perfectly fine to purchase a NFT for let's say 0.5 ETH and then later selling it for 5 ETH.</p>

<h2>What is ETH?</h2>

<p>ETH is the shortname of the cryptocurrency ether. Currently 1 ETH = <span style="text-decoration: line-through;">$1622</span> $2094 USD (changing daily, check <a href="https://www.google.com/search?q=price+of+eth" target="_blank">here</a>).</p>

<h2>What is NFT?</h2>

<p>A NFT (non-fungible token) is basically a digital certificate of ownership registered on the Ethereum blockchain. Instead of a coin (for example Bitcoin) a NFT is instead representing a digital object. NFTs are "one-of-a-kind" assets in the digital world that can be bought and sold like any other piece of property. The NFTs also prevent against fraud and fake cards, since all the authenticity of all transactions can be cryptographically verified and is public open information with an easy way to see who is the creator of the NFT.</p>

<h2>How to buy an NFT</h2>

<p>Click on the "Buy this NFT" link and then on the button Buy Now and follow the instructions.</p>

<h2>Why selling NFTs?</h2>

<ul>
    <li>I want a way to award early players of the game. I believe that all the early owners of these NFTs will make a very nice profit if they resell them in the future or sell the signed physical card.</li>
    <li>100% of all income obtained by selling these NFTs will be used for developing and marketing The Space War - further increasing the value of the NFTs.</li>
    <li>Obtaining money for the game this way at a so early stage is much better at this point than taking seed money from investors or doing a kickstarter. Investors and kickstarter is better at a later stage when there is some real traction to show.</li>
    <li>Hopefully also a good way to get the word out about the game without expensive costs involved.</li>
</ul>

<h2>Verification</h2>

<p>
    NFT Creator: <code>0x7897aef045c31882eac1717fab943703d1dd40e7</code> (<a href='https://opensea.io/accounts/TheSpaceWar' target='_blank'>TheSpaceWar</a>)<br>
    Contract Address: <code>0x495f947276749ce646f68ac8c248420045cb7b5e</code><br>
    Computer Code: <a href='https://github.com/JimWestergren/thespacewar.com/blob/master/include/nft-functions.php' target='_blank'>nft-functions.php</a> and <a href='https://github.com/JimWestergren/thespacewar.com/blob/master/content/first-edition.php' target='_blank'>first-edition.php</a>
</p>