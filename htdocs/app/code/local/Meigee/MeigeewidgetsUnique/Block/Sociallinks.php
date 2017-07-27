<?php

class Meigee_MeigeewidgetsUnique_Block_Sociallinks
extends Mage_Core_Block_Html_Link
implements Mage_Widget_Block_Interface
{
    protected function _construct() {
        parent::_construct();
    }
	protected function _toHtml() {
        return parent::_toHtml();  
    }

    public function getSocialLinks () {
        return $this->getData();
    }

	public function getTwitterCustColors(){
		return $this->getData('twitter_custom_colors');
	}
	
	public function getTwitterBg(){
		return $this->getData('twitter_bg');
	}
	
	public function getTwitterColor(){
		return $this->getData('twitter_color');
	}
	
	public function getTwitterBorder(){
		return $this->getData('twitter_border');
	}
	
	public function getRssCustColors(){
		return $this->getData('rss_custom_colors');
	}
	
	public function getRssBg(){
		return $this->getData('rss_bg');
	}
	
	public function getRssColor(){
		return $this->getData('rss_color');
	}
	
	public function getRssBorder(){
		return $this->getData('rss_border');
	}
		
	public function getFacebookCustColors(){
		return $this->getData('facebook_custom_colors');
	}
		
	public function getFacebookBg(){
		return $this->getData('facebook_bg');
	}
	
	public function getFacebookColor(){
		return $this->getData('facebook_color');
	}
	
	public function getFacebookBorder(){
		return $this->getData('facebook_border');
	}
	
	public function getGPlusCustColors(){
		return $this->getData('googleplus_custom_colors');
	}
	
	public function getGPlusBg(){
		return $this->getData('googleplus_bg');
	}
	
	public function getGPlusColor(){
		return $this->getData('googleplus_color');
	}
	
	public function getGPlusBorder(){
		return $this->getData('googleplus_border');
	}
	
	public function getLinkedinCustColors(){
		return $this->getData('linkedin_custom_colors');
	}
	
	public function getLinkedinBg(){
		return $this->getData('linkedin_bg');
	}
	
	public function getLinkedinColor(){
		return $this->getData('linkedin_color');
	}
	
	public function getLinkedinBorder(){
		return $this->getData('linkedin_border');
	}
	
	public function getPinterestCustColors(){
		return $this->getData('pinterest_custom_colors');
	}
	
	public function getPinterestBg(){
		return $this->getData('pinterest_bg');
	}
	
	public function getPinterestColor(){
		return $this->getData('pinterest_color');
	}
	
	public function getPinterestBorder(){
		return $this->getData('pinterest_border');
	}
	
	public function getVimeoCustColors(){
		return $this->getData('vimeo_custom_colors');
	}
	
	public function getVimeoBg(){
		return $this->getData('vimeo_bg');
	}
	
	public function getVimeoColor(){
		return $this->getData('vimeo_color');
	}
	
	public function getVimeoBorder(){
		return $this->getData('vimeo_border');
	}
	
	public function getYoutubeCustColors(){
		return $this->getData('youtube_custom_colors');
	}
	
	public function getYoutubeBg(){
		return $this->getData('youtube_bg');
	}
	
	public function getYoutubeColor(){
		return $this->getData('youtube_color');
	}
	
	public function getYoutubeBorder(){
		return $this->getData('youtube_border');
	}
	
	public function getFlickrCustColors(){
		return $this->getData('flickr_custom_colors');
	}
	
	public function getFlickrBg(){
		return $this->getData('flickr_bg');
	}
	
	public function getFlickrColor(){
		return $this->getData('flickr_color');
	}
	
	public function getFlickrBorder(){
		return $this->getData('flickr_border');
	}
	
	public function getInstagramCustColors(){
		return $this->getData('instagram_custom_colors');
	}
	
	public function getInstagramBg(){
		return $this->getData('instagram_bg');
	}
	
	public function getInstagramColor(){
		return $this->getData('instagram_color');
	}
	
	public function getInstagramBorder(){
		return $this->getData('instagram_border');
	}
	
}