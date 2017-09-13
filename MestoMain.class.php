<?php
class MestoMain {
	
  private static function getArticleData($article_title){
    global $wgOut;
    $context = $wgOut->getSkin();
      if ( htmlspecialchars($article_title)) {
        $art = Article::newFromTitle(Title::newFromText( htmlspecialchars($article_title)), $context);
      }
      else return 0;
      
      $rev = $art->getRevisionFetched(); 
      
      if($rev){
        $rev = $art->getRevisionFetched(); 
        $date = "";
        
        $date = $rev->getTimestamp();
        $date = date("d.m.Y",wfTimestamp(TS_UNIX, $date));
        $titl = $art->getTitle()->getBaseText(); 
      
        $ret = array(
          "date" => $date,
          "title" => $titl
        );

      return $ret;
      }
      
      else{
        return 0;
      }
  
  }

  private static function getImage($image){

    if($f = wfFindFile( htmlspecialchars($image) )){
      $imageUrl = $f->getCanonicalUrl();
    }
    else {
      $imageUrl = "";
    }  

    return $imageUrl;

  }

  static function onParserInit( Parser $parser ) {
		$parser->setHook( 'start-center-block', array( __CLASS__, 'startCenterRender' ) ); 
    $parser->setHook( 'end-center-block', array( __CLASS__, 'endCenterRender' ) ); 
    $parser->setHook( 'half-block', array( __CLASS__, 'halfBlockRender' ) ); 
    $parser->setHook( 'feature-block', array( __CLASS__, 'featureBlockRender' ) );
    $parser->setHook( 'daily-block', array( __CLASS__, 'dailyBlockRender' ) ); 
    $parser->setHook( 'news', array( __CLASS__, 'NewsRender' ) ); 
    $parser->setHook( 'theme-block', array( __CLASS__, 'themeBlockRender' ) );
    $parser->setHook( 'articles-block', array( __CLASS__, 'articlesBlockRender' ) );
    $parser->setHook( 'article', array( __CLASS__, 'articleRender' ) );  
    $parser->setHook( 'right-column', array( __CLASS__, 'rightColumnRender' ) );   
		return true;
	}
	
  static function featureBlockRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		
    $art_data = self::getArticleData( $args['title'] );

    if(!$art_data){
      return "<b>Что-то пошло, ребята, не так.</b>";
    }

    $imageUrl = self::getImage($args['image']);
    
      	$ret= '<div class="grid-x grid-margin-y grid-padding-x">
            <div class="photocard bignews cell grid-x" style="background: rgba(0, 0, 0, 0) url(\''.$imageUrl.'\') no-repeat scroll center center; background-size: cover;">         
                <div class="phcard__text-container cell align-self-bottom">
                   <a href="#"><h2 class="photocard__text-container__slog">'.$art_data['title'].'</h2></a>
                   <p class="photocard__text-container__text">'.htmlspecialchars($input).'</p>
                   <div class="btm grid-x">
                     <div class="photocard__text-container__timestamp cell small-6">
                        <h4 class="">'.$art_data['date'].'</h4>
                     </div>
                     <div class="card-footer-comments cell small-6">
                        <p><img src="assets/img/comments.svg"><a class="featured_a" href="#">255</a></p>
                     </div>
                   </div>
                </div>
            </div>
          </div>
          ';			  	
		return $ret;
	}

  static function startCenterRender( $input, array $args, Parser $parser, PPFrame $frame ) {
    $ret = '
                <div class="cell auto main-block-container">';

    return $ret;

  }

  static function endCenterRender( $input, array $args, Parser $parser, PPFrame $frame ) {
    $ret =  '</div>
         ';

    return $ret;

  }

  static function halfBlockRender( $input, array $args, Parser $parser, PPFrame $frame ) {

    $art_data = self::getArticleData( $args['title'] );

    if(!$art_data){
      return "<b>Что-то пошло, ребята, не так.</b>";
    }

    $imageUrl = self::getImage($args['image']);

    $ret = '<div class="smallcard cell small-12 medium-6">
              <div class="cardimage" style="background: url(\''.$imageUrl.'\') center center no-repeat"></div>
              <div class="flatcard-content">
                
                  <h3>'.$art_data['title'].'</h3>
                  <p class="lead-p">'.htmlspecialchars($input).'</p>
             
              </div>
              <div class="flatcard-footer grid-x align-middle">
                <div class="flatcard-footer-time cell small-6">'.$art_data['date'].'</div>
                <div class="flatcard-footer-comments cell small-6"><img src="assets/img/comments.svg"><a href="#">1</a></div>
              </div>           
            </div>';

    return $ret;

  }

 static function startTwoBlocks ( $input, array $args, Parser $parser, PPFrame $frame ) {
  $ret = '<div class="grid-x grid-margin-y grid-margin-x">';

  return $ret;
 }

 static function dailyBlockRender ( $input, array $args, Parser $parser, PPFrame $frame ) {
  $output = ' <div class="themeblock cell small-12">
              <div class="theme-body">
                <div class="theme-b-top">
                  <p>Повестка</p>
                </div>
                <div class="theme-b-body grid-x grid-margin-x grid-padding-y">
                  '.$input.'
                </div>
              </div>
            </div>';

  $ret = $parser->recursiveTagParse( $output, $frame );
  return $ret;
 }

 static function NewsRender ( $input, array $args, Parser $parser, PPFrame $frame ) {
  $art_data = self::getArticleData( $args['title'] );
  
  if(!$art_data){
     return "<b>Что-то пошло, ребята, не так.</b>";
  }
  $ret = ' <div class="short-news cell small-12 medium-6">
                    
                    <p class="sn-zag"><a href="">'.$art_data['title'].'</a></p>
                    <p class="sn-body">'.htmlspecialchars($input).'</p>
                  
                    <div class="grid-x">
                      <div class="cell auto sn-time">05.04.2017</div>
                      <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
                    </div>
            </div>';

  return $ret;
 } 


static function themeBlockRender ( $input, array $args, Parser $parser, PPFrame $frame ) {
  $art_data = self::getArticleData( $args['title'] );
  
  if(!$art_data){
     return "<b>Что-то пошло, ребята, не так.</b>";
  }

  $imageUrl = self::getImage($args['image']);

  $parse_input = $parser->recursiveTagParse( $input, $frame );
  $ret = ' <div class="themeblock cell small-12">
              <div class="theme-header grid-x" style="background: url(\''.$imageUrl.'\') center center no-repeat; background-size: cover;">
                <div class="theme-head-top">
                  <p>Обыск и свидание</p>
                </div>
                <div class="theme-head-text cell align-self-bottom">
                  <div class="phcard__text-container ">
                   <a href="#"><h2 class="photocard__text-container__slog">'.$art_data['title'].'</h2></a>
                   <p class="photocard__text-container__text"> Северный район зарастает мусором </p>
                   <div class="btm grid-x">
                     <div class="photocard__text-container__timestamp cell small-6">
                        <h4 class="">18.07.2017</h4>
                     </div>
                     <div class="card-footer-comments cell small-6">
                        <p><img src="assets/img/comments.svg"><a class="featured_a" href="#">255</a></p>
                     </div>
                   </div>
                </div>
                </div>
              </div>
              <div class="theme-body">
                <div class="theme-b-top">
                  <p>еще по теме</p>
                </div>
                <div class="theme-b-body grid-x grid-margin-x grid-padding-y">
                '.$parse_input.'
                </div>
              </div>
            </div>';

  
  return $ret;
 }

 static function articlesBlockRender ( $input, array $args, Parser $parser, PPFrame $frame ) {
  
  $parse_input = $parser->recursiveTagParse( $input, $frame );

  $ret = '<div class="articles-block cell small-12" style="background: rgba(0, 0, 0, 0) url(\'http://orl.ec/images/I23552234.jpg\') no-repeat scroll center center; background-size: cover;">
              <div class="a-block-top">
                <p>Политота</p>
              </div>
              <div class="a-block-body grid-x grid-padding-y grid-padding-x">
                '.$parse_input.'
              </div>
            </div>';

  return $ret;
 }

 static function articleRender ( $input, array $args, Parser $parser, PPFrame $frame ) {
  
  $art_data = self::getArticleData( $args['title'] );
  
  if(!$art_data){
     return "<b>Что-то пошло, ребята, не так.</b>";
  }

  $ret = '  <div class="cell small-12 medium-4">
                  <p><a href="">'.$art_data['title'].'</a><p>
            </div>';

  return $ret; 

 } 

 static function rightColumnRender( $input, array $args, Parser $parser, PPFrame $frame ) {
  
  

  $ret = '<div class="cell short-news-block">
          
          <div id="moveme" class="grid-y grid-padding-y grid-padding-x">
            <div class="snb-label"> </div>
            <div class="ad cell rb-reseller"><img src="assets/img/ad-sq.png" alt=""></div>
            <div class="short-news rb-reseller cell">
              <a href="">
              <p class="sn-zag">Моя полиция меня бережет</p>
              <p class="sn-body">Участников антикоррупционной прогулки хотят обвинить в предварительном сговоре</p>
              </a>
              <div class="grid-x">
                <div class="cell auto sn-time">05.04.2017</div>
                <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
              </div>
            </div>
            <div class="short-news rb-reseller cell">
              <a href="">
              <p class="sn-zag">Моя полиция меня бережет 2</p>
              <p class="sn-body">Участников антикоррупционной прогулки хотят обвинить в предварительном сговоре</p>
              </a>
              <div class="grid-x">
                <div class="cell auto sn-time">05.04.2017</div>
                <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
              </div>
            </div>
            <div class="short-news rb-reseller cell">
              <a href="">
              <p class="sn-zag">Моя полиция меня бережет 3</p>
              <p class="sn-body">Участников антикоррупционной прогулки хотят обвинить в предварительном сговоре</p>
              </a>
              <div class="grid-x">
                <div class="cell auto sn-time">05.04.2017</div>
                <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
              </div>
            </div>
            <div class="ad cell rb-reseller"><img src="assets/img/ad-pg.png" alt=""></div>
            <div class="short-news rb-reseller cell">
              <a href="">
              <p class="sn-zag">Моя полиция меня бережет 4</p>
              <p class="sn-body">Участников антикоррупционной прогулки хотят обвинить в предварительном сговоре</p>
              </a>
              <div class="grid-x">
                <div class="cell auto sn-time">05.04.2017</div>
                <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
              </div>
            </div>
            <div class="short-news rb-reseller cell">
              <a href="">
              <p class="sn-zag">Моя полиция меня бережет 5</p>
              <p class="sn-body">Участников антикоррупционной прогулки хотят обвинить в предварительном сговоре</p>
              </a>
              <div class="grid-x">
                <div class="cell auto sn-time">05.04.2017</div>
                <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
              </div>
            </div>
            <div class="ad cell rb-reseller"><img src="assets/img/ad-lt.png" alt=""></div>
            <div class="short-news rb-reseller cell">
              <a href="">
              <p class="sn-zag">Моя полиция меня бережет 6</p>
              <p class="sn-body">Участников антикоррупционной прогулки хотят обвинить в предварительном сговоре</p>
              </a>
              <div class="grid-x">
                <div class="cell auto sn-time">05.04.2017</div>
                <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
              </div>
            </div>
            <div class="short-news rb-reseller cell">
              <a href="">
              <p class="sn-zag">Моя полиция меня бережет 7</p>
              <p class="sn-body">Участников антикоррупционной прогулки хотят обвинить в предварительном сговоре</p>
              </a>
              <div class="grid-x">
                <div class="cell auto sn-time">05.04.2017</div>
                <div class="cell auto sn-comments text-right"><img src="assets/img/comments.png" alt="">235</div>
              </div>
            </div>
            <div class="ad cell rb-reseller"><img src="assets/img/ad-sq2.png" alt=""></div>
            <div class="allnews-link-container text-center">
              <a href="" class="button">все новости</a>
            </div>
          </div>
        </div>';

  return $ret;
 } 


}