/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

<!--悬浮音乐播放器-->
<div class="musicbc" id="musicbc">
    <!--背景图-->
        <img class="mimg" src="./assets/img/thumbnail.svg" data-src="./assets/img/musicba.jpg" alt="" id="ming">
        <div class="muka">
            <!--封面-->
            <div class="musicimg">
                <img src="./assets/img/thumbnail.svg" data-src="./assets/img/musicba.jpg" alt="" onclick="mbpy()" id="yszt" lang="0">
            </div>
            <div class="musiccz">
                <audio id="musicplay" src="" type="audio/mp3" controls="controls" style="display: none;" lang="0" class="">您的浏览器不支持音频元素。</audio>
                <div class="musiccz-btn" onclick="bfpy()"><i class="iconfont icon-jixu ri-z-sx" id="sh-musiccz-zb" lang="0" data-bfzt="bb"></i></div>
                <div class="musiccz-btn" onclick="bfpg()"><i class="iconfont icon-quxiao ri-z-sx" id="sh-musiccz-bf" lang="0" data-bfzt="bb" style="font-size: 18px;"></i></div>
            </div>
        </div>
</div>
