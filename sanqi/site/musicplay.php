/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

<!--音乐-->
<div class="music-play">
        <!-- 框架 -->
        <div class="music-play-conten">
            <!--背景图-->
                <img class="mimg" src="./assets/img/thumbnail.svg" data-src="<?php if($mus[3] ==""){echo "./assets/img/musicba.jpg";}else{echo $mus[3];}?>" alt="" style="width: 110%;height: 110%;position: absolute;/*left: -10px;*/pointer-events: none;filter: brightness(80%) saturate(150%) blur(10px);-webkit-filter: brightness(80%) saturate(150%) blur(10px);z-index: 0;">
            <!-- 左 -->
            <div class="music-play-conten-left">
                <div class="music-play-conten-left-bg" style="background-image: url(<?php if($mus[3] ==""){echo "./assets/img/musicba.jpg";}else{echo $mus[3];}?>);"></div>
            </div>
            <!-- 右 -->
            <div class="music-play-conten-right">
                <div class="music-play-conten-right-z">
                    <span class="conten-right-z-bt"><?php echo $mus[1]; ?></span>
                    <span class="conten-right-z-nr"><?php echo $mus[2]; ?></span>
                </div>
                <div class="music-play-conten-right-y">
                    <div class="conten-right-y-tb" lang="<?php echo $cid; ?>" onclick="audbf()"><i class="iconfont icon-jixu" id="sh-aud-left-plak-<?php echo $cid; ?>" lang="0"></i></div>
                    <!--歌曲信息-->
                    <i id="musicurl-<?php echo $cid; ?>" lang="<?php echo $mus[0]; ?>" class="<?php if($mus[3] ==""){echo "./assets/img/musicba.jpg";}else{echo $mus[3];}?>" style="display: none;"></i>
                </div>
            </div>
        </div>
</div>
