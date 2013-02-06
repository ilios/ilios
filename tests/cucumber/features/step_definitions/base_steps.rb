Given /^I am on the Ilios home page$/ do
  visit "/";
end

Then /^I should see "(.*?)"$/ do |text|
  should have_text text
end

Then /^"(.*?)" should contain "(.*?)"/ do |id, content|
  find("##{id}").should have_content(content)
end

Given /^I navigate to the "(.*?)" tab$/ do |tab_name|
  find(".tabs").click_link(tab_name)
end

Given /^I click the "(.*?)" link$/ do |link_text|
  click_link link_text
end

Given /^I click "(.*?)"$/ do |link_text|
  find("span", :text => link_text).click
end

Given /^I click the first "(.*?)"$/ do |link_text|
  first("span", :text => link_text).click
end

Given /^I click the "(.*?)" button$/ do |button_text|
  click_button button_text
end

Given /^I click the first element with class "(.*?)"$/ do |element_text|
  find(".#{element_text}").click
end

Given /^I enter "(.*?)" into "(.*?)"$/ do |content, field|
  fill_in field, :with => content
end

Given /^I set "(.*?)" to "(.*?)"$/ do |widget_name, value|
  find("##{widget_name}").set(value)
end

Given /^I log in as "(.*?)" with password "(.*?)"$/ do |user, pass|
  click_link "Login" 
  step "I enter \"#{user}\" into \"User Name\""
  step "I enter \"#{pass}\" into \"Password\""
  #This combined with the first line may suggest a usability issue...
  click_button "Login" 
end

Then /^there is a "(.*?)" class$/ do |class_name|
  should have_css("." + class_name)
end

Then /^there is no "(.*?)" class$/ do |class_name|
  should have_no_css("." + class_name)
end

Then /^I click the "(.*?)" button for "(.*?)"$/ do |button_text, section|
  find('.row', {:visible => true, :text => section}).click_link(button_text)
end

Then /^I select "(.*?)" from "(.*?)"$/ do |arg1, arg2|
  pending # express the regexp above with the code you wish you had
end

Then /^I click all expanded toggles$/ do
  all('.expanded .toggle').each { |toggle| toggle.click }
end